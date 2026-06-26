<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'project_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'invoice_amount',
        'tax_amount',
        'discount_amount',
        'final_amount',
        'paid_amount',
        'status',
    ];

    protected $casts = [
        'invoice_date'    => 'date',
        'due_date'        => 'date',
        'invoice_amount'  => 'decimal:2',
        'tax_amount'      => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount'    => 'decimal:2',
        'paid_amount'     => 'decimal:2',
    ];

    // relationships

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // computed attributes

    public function getDueAmountAttribute(): float
    {
        return (float) $this->final_amount - (float) $this->paid_amount;
    }

    //  helper methods

    public function hasPayments(): bool
    {
        return $this->payments()->exists();
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isEditable(): bool
    {
        return ! $this->hasPayments();
    }

    public function canReceivePayment(): bool
    {
        return ! $this->isCancelled() && $this->due_amount > 0;
    }


    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'draft'          => 'bg-gray-100 text-gray-700',
            'sent'           => 'bg-blue-100 text-blue-700',
            'partially_paid' => 'bg-yellow-100 text-yellow-700',
            'paid'           => 'bg-green-100 text-green-700',
            'cancelled'      => 'bg-red-100 text-red-700',
            default          => 'bg-gray-100 text-gray-700',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft'          => 'Draft',
            'sent'           => 'Sent',
            'partially_paid' => 'Partially Paid',
            'paid'           => 'Paid',
            'cancelled'      => 'Cancelled',
            default          => ucfirst($this->status),
        };
    }
}