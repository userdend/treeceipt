<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workspace extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'type',
        'tax_no',
        'registration_no',
    ];


    protected static function booted()
    {
        static::deleting(function ($workspace) {
            $workspace->receipts->each(function ($receipt) {
                $receipt->receiptItems()->withTrashed()->get()->each->delete();
                $receipt->delete();
            });
        });

        static::restoring(function ($workspace) {
            $workspace->receipts()->withTrashed()->get()->each(function ($receipt) {
                $receipt->restore();
                $receipt->receiptItems()->withTrashed()->get()->each->restore();
            });
        });

        static::forceDeleting(function ($workspace) {
            $workspace->receiptItems()->withTrashed()->forceDelete();
            $workspace->receipts()->withTrashed()->get()->each->forceDelete(); // ensures S3 cleanup runs per receipt
        });
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    public function receiptItems()
    {
        return $this->hasManyThrough(
            ReceiptItem::class,
            Receipt::class
        );
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'workspace_users');
    }
}
