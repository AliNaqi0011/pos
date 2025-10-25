<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Category extends Model
{
    use HasFactory, Notifiable;  // Add this line

    protected $fillable = ['name', 'description', 'parent_id', 'created_by'];

    protected static function booted()
    {
        static::creating(function ($category) {
            if (auth()->check() && !$category->created_by) {
                $category->created_by = auth()->id();
            }
        });

        static::addGlobalScope('dataScope', function ($builder) {
            $user = auth()->user();
            if (!$user) {
                return;
            }
            
            if ($user->hasRole('super_admin')) {
                $builder->whereRaw('1 = 0'); // Super admin manages system, not categories
                return;
            }
            
            if (config('app.has_created_by_column', true)) {
                $allowedIds = session('data_scope_user_ids', [$user->id]);
                $builder->whereIn('created_by', $allowedIds);
            }
        });
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
