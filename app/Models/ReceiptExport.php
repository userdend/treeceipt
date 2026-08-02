<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptExport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'file_path',
        'status',
        'total_receipts'
    ];

    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
