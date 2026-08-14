<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateReceiptExportJob;
use App\Models\ReceiptExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExportController extends Controller
{
    public function list(Request $request)
    {
        $perPage = $request->input('itemsPerPage', 10);
        $sortBy = $request->input('sortBy');
        $sortOrder = $request->input('sortOrder', 'asc');

        $allowedSorts = [
            'file_name',
            'status',
            'total_receipts',
            'updated_at'
        ];

        $query = ReceiptExport::select([
            'id',
            'user_id',
            'file_name',
            'status',
            'total_receipts',
            'updated_at'
        ])->where('user_id', auth()->id());

        if ($sortBy && in_array($sortBy, $allowedSorts, true)) {
            $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
        } else {
            $query
                ->orderBy('id', 'desc')
                ->orderBy('updated_at', 'desc');
        }

        $roles = $query->paginate($perPage);

        return response()->json([
            'data' => $roles->items(),
            'total' => $roles->total(),
        ]);
    }

    public function pdf(Request $request)
    {
        $export = ReceiptExport::create([
            'user_id' => auth()->id(),
            'file_name' => 'N/A',
            'status' => 'processing',
        ]);

        GenerateReceiptExportJob::dispatch($export);

        return response()->json([
            'message' => 'Export started',
            'export_id' => $export->id
        ]);
    }

    public function downloadPdf($id)
    {
        $userExport = ReceiptExport::find($id);

        abort_unless($userExport->user_id === auth()->id(), 403);

        if ($userExport->status !== 'completed') {
            return response()->json([
                'message' => 'Export is not ready yet.',
            ], 409);
        }

        $url = Storage::disk('s3')->temporaryUrl(
            $userExport->file_path,
            now()->addMinutes(10)
        );

        return response()->json([
            'url' => $url,
        ]);
    }
}
