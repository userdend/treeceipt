<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReceiptItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'receipt_id',
        'name',
        'price',
        'quantity',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }
}
