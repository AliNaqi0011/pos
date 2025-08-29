<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturnItem extends Model
{
    protected $fillable = [
        'purchase_return_id',
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

    // Optional relationships
    public function purchaseReturn()
    {
        return $this->belongsTo(PurchaseReturn::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
