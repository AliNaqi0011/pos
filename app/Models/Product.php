<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use App\Traits\Auditable;

class Product extends BaseModel
{
    use HasFactory, Auditable;

    // Define the fillable attributes
    protected $fillable = [
        'name', 
        'description', 
        'price', 
        'quantity', 
        'barcode', 
        'category_id', 
        'brand_id', 
        'warehouse_id', 
        'cost_price', 
        'sale_price', 
        'discount_price', 
        'stock_alert_level',
        'image',
        'created_by',
        'tenant_id'
    ];

    protected static function booted()
    {
        static::creating(function ($product) {
            if (auth()->check() && !$product->created_by) {
                $product->created_by = auth()->id();
            }
        });

        static::addGlobalScope('dataScope', function ($builder) {
            $user = auth()->user();
            if (!$user) {
                return;
            }
            
            // Super admin manages system, not products - redirect them
            if ($user->hasRole('super_admin')) {
                $builder->whereRaw('1 = 0'); // No products for super admin
                return;
            }
            
            // Check if created_by column exists before applying scope
            if (config('app.has_created_by_column', true)) {
                $allowedIds = session('data_scope_user_ids', [$user->id]);
                $builder->whereIn('created_by', $allowedIds);
            }
        });
    }

    // Define relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    // Helper method to generate a barcode (for simplicity)
    public static function generateBarcode($productId)
    {
        return 'PROD-' . str_pad($productId, 6, '0', STR_PAD_LEFT);
    }

    // Accessor for the image (to create a full URL for the stored image)
    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::url($this->image) : null;
    }



    // Helper method to check if the product is below the stock alert level
    public function isStockLow()
    {
        return $this->quantity <= ($this->stock_alert_level ?? 0);
    }

    // Scopes for more complex queries (optional but useful)
    public function scopeInStock($query)
    {
        return $query->where('quantity', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('quantity', 0);
    }

    public function scopeOnSale($query)
    {
        return $query->where('sale_price', '>', 0);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
    
    public function decreaseStock(int $quantity): void
    {
        $this->quantity = max(0, $this->quantity - $quantity);
        $this->save();
    }
    
    public function increaseStock(int $quantity): void
    {
        $this->quantity += $quantity;
        $this->save();
    }
}
