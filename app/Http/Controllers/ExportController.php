<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateReceiptExportJob;
use App\Models\ReceiptExport;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function list(Request $request)
    {
        $perPage = $request->input('itemsPerPage', 10);
        $sortBy = $request->input('sortBy');
        $sortOrder = $request->input('sortOrder', 'asc');

        $allowedSorts = [
            'file_path',
            'status',
            'total_receipts'
        ];

        $query = ReceiptExport::select([
            'id',
            'file_name',
            'status',
            'total_receipts'
        ]);

        if ($sortBy && in_array($sortBy, $allowedSorts, true)) {
            $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
        } else {
            $query
                ->orderBy('id', 'desc')
                ->orderBy('created_at', 'desc');
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
            'status' => 'processing',
        ]);

        GenerateReceiptExportJob::dispatch($export);

        return response()->json([
            'message' => 'Export started',
            'export_id' => $export->id
        ]);
    }

    public function download()
    {
    }
}
