<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['fee_invoice_id', 'fee_type_id', 'amount'])]
class FeeInvoiceItem extends Model
{
    public function invoice(): BelongsTo { return $this->belongsTo(FeeInvoice::class, 'fee_invoice_id'); }
    public function feeType(): BelongsTo { return $this->belongsTo(FeeType::class); }
}
