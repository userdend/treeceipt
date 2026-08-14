<?php

namespace App\Jobs;

use App\Models\Receipt;
use App\Models\ReceiptExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GenerateReceiptExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public function __construct(
        public ReceiptExport $export
    ) {
    }

    public function handle()
    {
        try {
            $receipts = Receipt::whereHas('workspace.users', function ($query) {
                $query->where('users.id', $this->export->user_id);
            })
                ->get();

            $receipts->each(function ($receipt) {
                try {
                    $mimeType = Storage::disk('s3')->mimeType($receipt->file_path);
                    $contents = Storage::disk('s3')->get($receipt->file_path);
                    $receipt->image_src = "data:{$mimeType};base64," . base64_encode($contents);
                } catch (\Throwable $e) {
                    $receipt->image_src = null; // handle in blade with a placeholder
                }
            });

            $pdf = Pdf::loadView(
                'receipts.export',
                [
                    'receipts' => $receipts
                ]
            );

            $timestamp = now()->format('Y-m-d_H-i-s-u');

            $name = "receipts_{$timestamp}.pdf";
            $path = "exports/user_{$this->export->user_id}/{$name}";

            Storage::disk('s3')->put(
                $path,
                $pdf->output()
            );

            $this->export->update([
                'file_name' => $name,
                'file_path' => $path,
                'status' => 'completed',
                'total_receipts' => $receipts->count()
            ]);
        } catch (\Throwable $e) {
            $this->export->update([
                'status' => 'failed'
            ]);

            throw $e;
        }
    }
}
