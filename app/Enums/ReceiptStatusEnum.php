<?php

namespace App\Enums;

enum ReceiptStatusEnum: string
{
    case Uploaded = 'uploaded';
    case Processing = 'processing';
    case Review = 'review';
    case Completed = 'completed';
    case Failed = 'failed';
}
