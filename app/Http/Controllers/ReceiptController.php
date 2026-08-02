<?php

namespace App\Http\Controllers;

use App\Enums\ReceiptStatusEnum;
use App\Jobs\GenerateReceiptExportJob;
use App\Jobs\ProcessReceiptOCR;
use App\Models\Receipt;
use App\Models\ReceiptExport;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Storage;

class ReceiptController extends Controller
{
    public function __construct()
    {
    }

    public function data($id)
    {
        $receipt = Receipt::select([
            'id',
            'workspace_id',
            'merchant',
            'total',
            'currency',
            'receipt_date',
            'file_path',
            'status',
        ])
            ->with('items')
            ->whereHas('workspace.users', function ($query) {
                $query->where('users.id', auth()->id());
            })
            ->where('id', $id)
            ->firstOrFail();

        $receipt->image_url = $receipt->file_path
            ? Storage::disk('s3')->temporaryUrl(
                $receipt->file_path,
                now()->addMinutes(10)
            )
            : null;

        return response()->json($receipt);
    }

    public function list(Request $request)
    {
        $perPage = $request->input('itemsPerPage', 10);
        $sortBy = $request->input('sortBy');
        $sortOrder = $request->input('sortOrder', 'asc');

        $allowedSorts = ['id', 'merchant', 'currency', 'total', 'receipt_date'];

        $query = Receipt::select(['id', 'merchant', 'currency', 'total', 'receipt_date'])
            ->whereHas('workspace.users', function ($query) {
                $query->where('users.id', auth()->id());
            })
            ->where('workspace_id', $request->workspaceId)
            ->whereNot('status', ReceiptStatusEnum::Failed);

        if ($sortBy && in_array($sortBy, $allowedSorts, true)) {
            $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
        } else {
            $query
                ->orderBy('id', 'desc')
                ->orderBy('receipt_date', 'desc');
        }

        $receipts = $query->paginate($perPage);

        return response()->json([
            'data' => $receipts->items(),
            'total' => $receipts->total(),
        ]);
    }

    public function pending(Request $request)
    {
        $perPage = $request->input('itemsPerPage', 10);
        $sortBy = $request->input('sortBy');
        $sortOrder = $request->input('sortOrder', 'asc');

        $allowedSorts = ['id', 'status', 'ocr_error_code'];

        $query = Receipt::select(['id', 'status', 'ocr_error_code'])
            ->whereHas('workspace.users', function ($query) {
                $query->where('users.id', auth()->id());
            })
            ->where('workspace_id', $request->workspaceId)
            ->where('status', ReceiptStatusEnum::Failed);

        if ($sortBy && in_array($sortBy, $allowedSorts, true)) {
            $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('receipt_date', 'desc');
        }

        $receipts = $query->paginate($perPage);

        return response()->json([
            'data' => $receipts->items(),
            'total' => $receipts->total(),
        ]);
    }

    public function pendingCount()
    {
        return Receipt::where('status', ReceiptStatusEnum::Failed)
            ->count();
    }

    public function bin(Request $request)
    {
        $perPage = $request->input('itemsPerPage', 10);
        $sortBy = $request->input('sortBy');
        $sortOrder = $request->input('sortOrder', 'asc');

        $allowedSorts = ['id', 'merchant', 'currency', 'total', 'receipt_date'];

        $query = Receipt::select(['id', 'merchant', 'currency', 'total', 'receipt_date'])
            ->whereHas('workspace.users', function ($query) {
                $query->where('users.id', auth()->id());
            })
            ->where('workspace_id', $request->workspaceId)
            ->onlyTrashed();

        if ($sortBy && in_array($sortBy, $allowedSorts, true)) {
            $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('receipt_date', 'desc');
        }

        $receipts = $query->paginate($perPage);

        return response()->json([
            'data' => $receipts->items(),
            'total' => $receipts->total(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $receipt = Receipt::whereHas('workspace.users', function ($query) {
            $query->where('users.id', auth()->id());
        })
            ->where('id', $id)
            ->firstOrFail();

        $validated = $request->validate([
            'merchant' => 'required|string|max:255',
            'receiptDate' => 'required|date',
            'currency' => 'required|string|max:10',
            'total' => 'required|numeric',
            'items' => 'array',
            'items.*.id' => 'nullable|integer|exists:receipt_items,id',
            'items.*.name' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $receipt->update([
                'merchant' => $validated['merchant'],
                'receipt_date' => $validated['receiptDate'],
                'currency' => $validated['currency'],
                'total' => $validated['total'],
            ]);

            $incomingIds = [];

            foreach ($validated['items'] as $itemData) {
                if (!empty($itemData['id'])) {
                    // existing item — update it, but only if it belongs to this receipt
                    $item = $receipt->items()->where('id', $itemData['id'])->first();

                    if ($item) {
                        $item->update([
                            'name' => $itemData['name'],
                            'quantity' => $itemData['quantity'],
                            'price' => $itemData['price'],
                        ]);
                        $incomingIds[] = $item->id;
                    }
                } else {
                    // new item — create it
                    $newItem = $receipt->items()->create([
                        'name' => $itemData['name'],
                        'quantity' => $itemData['quantity'],
                        'price' => $itemData['price'],
                    ]);
                    $incomingIds[] = $newItem->id;
                }
            }

            $receipt->items()->whereNotIn('id', $incomingIds)->delete();

            DB::commit();

            return response()->json([
                'message' => 'Successfully update receipt',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json([
                'message' => 'Failed to update receipt',
            ], 500);
        }
    }

    public function delete($id)
    {
        $receipt = Receipt::whereHas('workspace.users', function ($query) {
            $query->where('users.id', auth()->id());
        })
            ->where('id', $id)
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $receipt->delete();

            DB::commit();

            return response()->json([
                'message' => 'Successfully delete receipt',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json([
                'message' => 'Failed to delete receipt',
            ], 500);
        }
    }

    public function forceDelete($id)
    {
        $receipt = Receipt::whereHas('workspace.users', function ($query) {
            $query->where('users.id', auth()->id());
        })
            ->where('id', $id)
            ->onlyTrashed()
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $receipt->forceDelete();

            DB::commit();

            return response()->json([
                'message' => 'Successfully delete receipt',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json([
                'message' => 'Failed to delete receipt',
            ], 500);
        }
    }

    public function forceDeleteFailed($id)
    {
        $receipt = Receipt::whereHas('workspace.users', function ($query) {
            $query->where('users.id', auth()->id());
        })
            ->where('id', $id)
            ->where('status', ReceiptStatusEnum::Failed)
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $receipt->forceDelete();

            DB::commit();

            return response()->json([
                'message' => 'Successfully delete receipt',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json([
                'message' => 'Failed to delete receipt',
            ], 500);
        }
    }

    public function restore($id)
    {
        $receipt = Receipt::whereHas('workspace.users', function ($query) {
            $query->where('users.id', auth()->id());
        })
            ->where('id', $id)
            ->onlyTrashed()
            ->firstOrFail();

        if ($receipt->workspace->withTrashed()->first()?->trashed()) {
            throw new Exception('Cannot restore a receipt while its workspace is deleted.');
        }

        DB::beginTransaction();
        try {
            $receipt->restore();

            DB::commit();

            return response()->json([
                'message' => 'Successfully restored receipt',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json([
                'message' => 'Failed to restored receipt',
            ], 500);
        }
    }

    public function upload(Request $request)
    {
        $validated = $request->validate([
            'workspace_id' => [
                'required',
                Rule::exists('workspace_users', 'workspace_id')
                    ->where(fn($query) => $query->where('user_id', auth()->id())),
            ],
            'file' => [
                'required',
                'file',
                'image',
                'mimes:png,jpg,jpeg',
                'max:10240', // 10 MB
            ],
        ]);

        $path = null;
        $file = $request->file('file');
        $path = $file->store(
            "receipts/{$validated['workspace_id']}",
            's3'
        );
        DB::beginTransaction();
        try {
            $receipt = Receipt::create([
                'workspace_id' => $validated['workspace_id'],
                'merchant' => null,
                'total' => null,
                'currency' => null,
                'receipt_date' => null,

                'file_path' => $path,
                'ocr_data' => null,

                'status' => ReceiptStatusEnum::Processing,
            ]);

            DB::commit();

            ProcessReceiptOCR::dispatch($receipt);

            return response()->json([
                'message' => 'Receipt uploaded successfully.',
                'data' => [
                    'id' => $receipt->id,
                    'status' => $receipt->status->value,
                ],
            ], 201);
        } catch (Exception $e) {
            // Delete from s3
            DB::rollBack();

            if ($path) {
                Storage::disk('s3')->delete($path);
            }

            report($e);

            return response()->json([
                'message' => 'Failed to upload receipt',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function replace(Request $request, $id)
    {
        $validated = $request->validate([
            'workspace_id' => [
                'required',
                Rule::exists('workspace_users', 'workspace_id')
                    ->where(fn($query) => $query->where('user_id', auth()->id())),
            ],
            'file' => [
                'required',
                'file',
                'image',
                'mimes:png,jpg,jpeg',
                'max:10240', // 10 MB
            ],
        ]);

        $path = null;
        $file = $request->file('file');
        $path = $file->store(
            "receipts/{$validated['workspace_id']}",
            's3'
        );
        DB::beginTransaction();
        try {
            $receipt = Receipt::whereHas('workspace.users', function ($query) {
                $query->where('users.id', auth()->id());
            })
                ->where('id', $id)
                ->where('status', ReceiptStatusEnum::Failed)
                ->firstOrFail();

            $receipt->update([
                'file_path' => $path,
                'status' => ReceiptStatusEnum::Processing
            ]);

            DB::commit();

            ProcessReceiptOCR::dispatch($receipt);

            return response()->json([
                'message' => 'Receipt uploaded successfully.',
                'data' => [
                    'id' => $receipt->id,
                    'status' => $receipt->status->value,
                ],
            ], 201);
        } catch (Exception $e) {
            // Delete from s3
            DB::rollBack();

            if ($path) {
                Storage::disk('s3')->delete($path);
            }

            report($e);

            return response()->json([
                'message' => 'Failed to upload receipt',
            ], 500);
        }
    }

    public function status($id)
    {
        $receipt = Receipt::select(['status', 'ocr_error_code'])
            ->where('id', $id)
            ->whereHas('workspace.users', function ($query) {
                $query->where('users.id', auth()->id());
            })
            ->firstOrFail();

        return response()->json($receipt);
    }
}
