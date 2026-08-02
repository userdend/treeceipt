<?php

namespace App\Jobs;

use App\Enums\ReceiptErrorEnum;
use App\Enums\ReceiptStatusEnum;
use App\Models\Receipt;
use App\Models\ReceiptItem;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use OpenAI;
use OpenAI\Exceptions\ErrorException;
use Throwable;

class ProcessReceiptOCR implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [10, 30, 60];
    public $timeout = 60; // Less than retry_after (queue.php)
    public $failOnTimeout = true;

    /**
     * Create a new job instance.
     */
    public function __construct(public Receipt $receipt)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $client = OpenAI::client(config('services.openai.key'));
        $receipt = $this->receipt->refresh();

        if ($receipt->status !== ReceiptStatusEnum::Processing) {
            return;
        }

        try {
            $mime = Storage::disk('s3')->mimeType($receipt->file_path);
            $contents = Storage::disk('s3')->get($receipt->file_path);
            $base64 = base64_encode($contents);
            $prompt = <<<PROMPT
                You are a receipt extraction assistant.

                Analyze the receipt image and extract the information into JSON.

                Rules:
                - Return ONLY valid JSON.
                - Do not include markdown or explanations.
                - Do not guess missing information.
                - The image should contain exactly one receipt.
                - If multiple receipts are detected, do not merge them.
                - Return an error indicating multiple receipts were found.
                - If no receipt is detected, return an error indicating no receipt was found.
                - If the receipt cannot be read clearly, return null for uncertain fields.
                - Do not infer values from partial text.
                - Only extract total if explicitly shown.
                - Do not calculate totals unless instructed.
                - Preserve the currency exactly as displayed.
                - Infer the ISO 4217 currency code (e.g. "USD", "MYR", "EUR") from any currency symbol, abbreviation, or context clues on the receipt (e.g. merchant location, symbol used).
                - If the currency cannot be reasonably determined, return null.
                - Do not convert the amount between currencies — only identify which currency it's in.
                - Convert recognizable dates into DD-MM-YYYY format.
                - If the date format is ambiguous, return null.
                - Return an empty array when no items are listed.
                - If the document is not a receipt or purchase document, return an empty receipts array.
                - Put any additional labeled information you find on the receipt that doesn't fit the fields above into other_fields as key-value pairs, using a short snake_case key describing what it is.

                JSON format:

                {
                    "success": true,
                    "merchant": "string|null",
                    "receipt_date": "DD-MM-YYYY|null",
                    "currency": "ISO 4217 code, e.g. USD|null",
                    "currency_raw": "currency symbol/text exactly as shown on receipt|string|null",
                    "total": "number|null",
                    "other_fields": {
                        "key": "value"
                    },
                    "items": [
                        {
                            "name": "string",
                            "quantity": "number|null",
                            "price": "number|null"
                        }
                    ]
                }

                Error response format:

                {
                    "success": false,
                    "error": "multiple_receipts_detected|no_receipt_detected|unreadable_receipt"
                }
                PROMPT;

            $response = $client->responses()->create([
                'model' => 'gpt-4.1-mini',
                'input' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'input_text',
                                'text' => $prompt,
                            ],
                            [
                                'type' => 'input_image',
                                'image_url' => "data:{$mime};base64,{$base64}",
                            ],
                        ],
                    ],
                ],
            ]);

            $text = $response->output[0]->content[0]->text;

            $data = json_decode($text, true);

            if (!is_array($data)) {
                throw new Exception('Invalid JSON returned.');
            }

            if (($data['success'] ?? false) === false) {
                $errorCode = match ($data['error'] ?? null) {
                    'multiple_receipts_detected' => ReceiptErrorEnum::MultipleReceiptsDetected->value,
                    'no_receipt_detected' => ReceiptErrorEnum::NoReceiptDetected->value,
                    'unreadable_receipt' => ReceiptErrorEnum::UnreadableReceipt->value,
                    default => null,
                };

                $receipt->update([
                    'ocr_data' => $data,
                    'ocr_error_code' => $errorCode,
                    'status' => ReceiptStatusEnum::Failed,
                ]);

                Storage::disk('s3')->delete($receipt->file_path);
                return;
            }

            $total = is_numeric($data['total'] ?? null) ? (float) $data['total'] : null;

            $currency = null;
            if (!empty($data['currency']) && preg_match('/^[A-Z]{3}$/', $data['currency'])) {
                $currency = $data['currency'];
            }

            $receiptDate = null;
            if (!empty($data['receipt_date'])) {
                try {
                    $receiptDate = Carbon::createFromFormat('d-m-Y', $data['receipt_date'])->toDateString();
                } catch (Throwable) {
                    $receiptDate = null;
                }
            }

            $receipt->update([
                'merchant' => $data['merchant'] ?? null,
                'total' => $total ?? null,
                'currency' => $currency ?? null,
                'receipt_date' => $receiptDate ?? null,
                'ocr_data' => $data,
                'ocr_response' => $response->toArray() ?? null,
                'status' => ReceiptStatusEnum::Review,
            ]);

            $receiptItems = [];

            foreach ($data['items'] as $item) {
                $receiptItems[] = [
                    'receipt_id' => $receipt->id,
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity']
                ];
            }

            ReceiptItem::insert($receiptItems);
        } catch (ErrorException $e) {
            report($e);
            throw $e;
        } catch (Throwable $e) {
            report($e);
            $this->fail($e);
        }
    }

    public function failed(Throwable $e): void
    {
        $this->receipt
            ->fresh()
                ?->update(['status' => ReceiptStatusEnum::Failed]);
    }
}
