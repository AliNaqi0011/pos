<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChartOfAccounts extends BaseModel
{
    use HasFactory, Auditable;

    protected $fillable = [
        'code',
        'name',
        'type',
        'subtype',
        'parent_id',
        'is_active',
        'tenant_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(ChartOfAccounts::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ChartOfAccounts::class, 'parent_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}