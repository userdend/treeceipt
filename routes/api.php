<?php

use App\Http\Controllers\ExportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Http\Request;
use App\Http\Controllers\ReceiptController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::prefix('receipts')->group(function () {
        Route::get('/data/{id}', [ReceiptController::class, 'data'])->name('receipt.data');
        Route::put('/data/{id}', [ReceiptController::class, 'update'])->name('receipt.data.update');
        Route::delete('/data/{id}', [ReceiptController::class, 'delete'])->name('receipt.data.delete');
        Route::patch('/data/{id}/restore', [ReceiptController::class, 'restore'])->name('receipt.data.restore');
        Route::delete('/data/{id}/force', [ReceiptController::class, 'forceDelete'])->name('receipt.data.delete.force');
        Route::delete('/data/{id}/force/failed', [ReceiptController::class, 'forceDeleteFailed'])->name('receipt.data.delete.force.failed');
        Route::post('/upload', [ReceiptController::class, 'upload'])->name('receipt.upload');
        Route::post('/replace/{id}', [ReceiptController::class, 'replace'])->name('receipt.replace');
        Route::get('/status/{id}', [ReceiptController::class, 'status'])->name('receipt.status');
        Route::get('/list', [ReceiptController::class, 'list'])->name('receipt.list');
        Route::get('/pending', [ReceiptController::class, 'pending'])->name('receipt.pending');
        Route::get('/pending/count', [ReceiptController::class, 'pendingCount'])->name('receipt.pending.count');
        Route::get('/bin', [ReceiptController::class, 'bin'])->name('receipt.bin');
    });

    Route::prefix('workspaces')->group(function () {
        Route::get('/data/{id}', [WorkspaceController::class, 'data'])->name('workspace.data');
        Route::put('/data/{id}', [WorkspaceController::class, 'update'])->name('workspace.data.update');
        Route::delete('/data/{id}', [WorkspaceController::class, 'delete'])->name('workspace.data.delete');
        Route::patch('/data/{id}/restore', [WorkspaceController::class, 'restore'])->name('workspace.data.restore');
        Route::delete('/data/{id}/force', [WorkspaceController::class, 'forceDelete'])->name('workspace.data.delete.force');
        Route::post('/store', [WorkspaceController::class, 'store'])->name('workspace.store');
        Route::get('/list', [WorkspaceController::class, 'list'])->name('workspace.list');
        Route::get('/list/menu', [WorkspaceController::class, 'listMenu'])->name('workspace.list.menu');
        Route::get('/bin', [WorkspaceController::class, 'bin'])->name('workspace.bin');
    });

    Route::prefix('roles')->group(function () {
        Route::get('/list', [RoleController::class, 'list'])->name('role.list');
    });

    Route::prefix('exports')->group(function () {
        Route::get('/list', [ExportController::class, 'list'])->name('export.list');
        Route::post('/pdf', [ExportController::class, 'pdf'])->name('export.pdf');
        Route::get('/download/pdf/{id}', [ExportController::class, 'downloadPdf'])->name('export.pdf.download');
    });
});
