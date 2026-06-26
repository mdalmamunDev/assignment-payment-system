<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'customer_id',
        'project_name',
        'project_code',
        'start_date',
        'deadline',
        'budget_amount',
        'status',
    ];

    protected $casts = [
        'start_date'    => 'date',
        'deadline'      => 'date',
        'budget_amount' => 'decimal:2',
    ];

    // relationships

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    //  helper methods

    public function hasInvoices(): bool
    {
        return $this->invoices()->exists();
    }
}