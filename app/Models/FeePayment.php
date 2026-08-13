<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['fee_invoice_id', 'amount_paid', 'payment_method', 'payment_date', 'received_by', 'transaction_ref'])]
class FeePayment extends Model
{
    public function invoice(): BelongsTo { return $this->belongsTo(FeeInvoice::class, 'fee_invoice_id'); }
    public function receivedBy(): BelongsTo { return $this->belongsTo(User::class, 'received_by'); }
}
