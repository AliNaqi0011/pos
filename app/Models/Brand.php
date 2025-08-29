<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    // Define the table name
    protected $table = 'brands';

    // Specify which attributes are mass assignable
    protected $fillable = ['name', 'slug', 'created_by'];

    protected static function booted()
    {
        static::creating(function ($brand) {
            if (auth()->check() && !$brand->created_by) {
                $brand->created_by = auth()->id();
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
            
            if (\Schema::hasColumn('brands', 'created_by')) {
                $allowedIds = session('data_scope_user_ids', [$user->id]);
                $builder->whereIn('created_by', $allowedIds);
            }
        });
    }
}
