<?php
// app/Models/Purchase.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'warehouse_id',
        'tax_rate',
        'tax_amount',
        'discount',
        'shipping',
        'grand_total',
        'received_amount',
        'paid_amount',
        'payment_type',
        'status',
        'payment_status',
        'notes',
        'reference_code',
    ];
    
    protected $casts = [
        'date' => 'datetime',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }
    
    public function returns()
    {
        return $this->hasMany(PurchaseReturn::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
