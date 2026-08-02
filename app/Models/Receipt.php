<?php

namespace App\Models;

use App\Enums\ReceiptStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Storage;

class Receipt extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'workspace_id',
        'merchant',
        'total',
        'currency',
        'receipt_date',
        'file_path',
        'ocr_data',
        'ocr_response',
        'ocr_error_code',
        'status',
    ];

    protected $casts = [
        'receipt_date' => 'date',
        'ocr_data' => 'array',
        'status' => ReceiptStatusEnum::class,
        'total' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::deleting(function ($receipt) {
            $receipt->items()->delete();
        });

        static::restoring(function ($receipt) {
            $receipt->items()->withTrashed()->restore();
        });

        static::forceDeleting(function ($receipt) {
            $receipt->items()->withTrashed()->forceDelete();
        });

        static::forceDeleted(function ($receipt) {
            if ($receipt->file_path) {
                Storage::disk('s3')->delete($receipt->file_path);
            }
        });
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function items()
    {
        return $this->hasMany(ReceiptItem::class);
    }
}
