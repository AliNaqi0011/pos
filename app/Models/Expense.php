<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Expense extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'date', 'warehouse_id', 'expense_category_id', 'amount', 'reference_code', 'title', 'details'
    ];

    protected $casts = [
        'date' => 'date',
    ];
    
    protected static function booted()
    {
        static::creating(function ($expense) {
            if (auth()->check() && !$expense->created_by) {
                $expense->created_by = auth()->id();
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
    
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    
    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }
    
    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }
}