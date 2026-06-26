<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company_name',
        'address',
        'status',
    ];

    // relationships

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    // helper methods

    public function hasProjects(): bool
    {
        return $this->projects()->exists();
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}