<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'payment_date',
        'amount',
        'payment_method',
        'reference_no',
        'note',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    // relationships

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    //  helper methods

    public function getMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'cash'           => 'Cash',
            'bank'           => 'Bank Transfer',
            'mobile_banking' => 'Mobile Banking',
            default          => ucfirst($this->payment_method),
        };
    }
}