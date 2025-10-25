<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ExpenseCategory extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'description', 'created_by'];

    protected static function booted()
    {
        static::creating(function ($category) {
            if (auth()->check() && !$category->created_by) {
                $category->created_by = auth()->id();
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
}