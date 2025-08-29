<?php
// app/Models/PurchaseItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'product_id',
        'product_cost',
        'net_unit_cost',
        'tax_type',
        'tax_value',
        'tax_amount',
        'discount_type',
        'discount_value',
        'discount_amount',
        'purchase_unit',
        'quantity',
        'sub_total',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
