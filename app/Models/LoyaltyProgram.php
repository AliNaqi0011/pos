<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyProgram extends BaseModel
{
    use HasFactory, Auditable;

    protected $fillable = [
        'name',
        'description',
        'points_per_currency',
        'currency_per_point',
        'min_points_redeem',
        'start_date',
        'end_date',
        'is_active',
        'tenant_id',
    ];

    protected $casts = [
        'points_per_currency' => 'decimal:2',
        'currency_per_point' => 'decimal:4',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function customerLoyaltyPoints(): HasMany
    {
        return $this->hasMany(CustomerLoyaltyPoints::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('start_date', '<=', now())
                    ->where(function($q) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', now());
                    });
    }
}