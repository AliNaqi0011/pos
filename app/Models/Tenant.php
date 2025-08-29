<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'domain',
        'database',
        'status',
        'subscription_plan_id',
        'subscription_ends_at',
        'trial_ends_at',
        'settings'
    ];

    protected $casts = [
        'subscription_ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'settings' => 'array'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function subscriptionPlan()
    {
        return $this->belongsTo(Plan::class, 'subscription_plan_id');
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isOnTrial()
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function subscriptionActive()
    {
        return $this->subscription_ends_at && $this->subscription_ends_at->isFuture();
    }
}