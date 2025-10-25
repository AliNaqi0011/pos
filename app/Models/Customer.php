<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable; // ✅ Add this import

class Customer extends Model
{
    use HasFactory, Notifiable; // ✅ Enables notifications

    protected $fillable = [
        'name', 'email', 'phone', 'address', 'city', 'state', 'zip', 'country', 'opening_balance', 'created_by'
    ];

    protected static function booted()
    {
        static::creating(function ($customer) {
            // Log every customer creation attempt with stack trace
            \Log::emergency('CUSTOMER CREATION DETECTED', [
                'customer_data' => $customer->toArray(),
                'user_id' => auth()->id(),
                'stack_trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10),
                'request_url' => request()->url(),
                'request_method' => request()->method(),
                'request_data' => request()->all()
            ]);
            
            if (auth()->check() && !$customer->created_by) {
                $customer->created_by = auth()->id();
            }
        });

        // Temporarily disable data scope
        // static::addGlobalScope('dataScope', function ($builder) {
        //     $user = auth()->user();
        //     if (!$user) {
        //         return;
        //     }
        //     
        //     if ($user->role === 'super_admin') {
        //         $builder->whereRaw('1 = 0');
        //         return;
        //     }
        //     
        //     if (config('app.has_created_by_column', true)) {
        //         $allowedIds = session('data_scope_user_ids', [$user->id]);
        //         $builder->whereIn('created_by', $allowedIds);
        //     }
        // });
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
