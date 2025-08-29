<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable; // ✅ Add this import

class Customer extends Model
{
    use Notifiable; // ✅ Enables notifications

    protected $fillable = [
        'name', 'email', 'phone', 'address', 'city', 'state', 'zip', 'country', 'opening_balance', 'created_by'
    ];

    protected static function booted()
    {
        static::creating(function ($customer) {
            if (auth()->check() && !$customer->created_by) {
                $customer->created_by = auth()->id();
            }
        });

        static::addGlobalScope('dataScope', function ($builder) {
            $user = auth()->user();
            if (!$user) {
                return;
            }
            
            if ($user->hasRole('super_admin')) {
                $builder->whereRaw('1 = 0');
                return;
            }
            
            if (\Schema::hasColumn('customers', 'created_by')) {
                $allowedIds = session('data_scope_user_ids', [$user->id]);
                $builder->whereIn('created_by', $allowedIds);
            }
        });
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
