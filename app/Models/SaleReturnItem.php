<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleReturnItem extends Model
{
    protected $fillable = [
        'sale_return_id', 'product_id', 'quantity', 'total',
    ];

    public function return()
    {
        return $this->belongsTo(SaleReturn::class, 'sale_return_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
