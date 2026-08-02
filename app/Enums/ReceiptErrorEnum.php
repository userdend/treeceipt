<?php

namespace App\Enums;

enum ReceiptErrorEnum: string
{
    case MultipleReceiptsDetected = 'multiple_receipts_detected';
    case NoReceiptDetected = 'no_receipt_detected';
    case UnreadableReceipt = 'unreadable_receipt';
}
