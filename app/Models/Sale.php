<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    // Update constants to match the values in the migration
    const STATUS_DRAFT = 'draft';
    const STATUS_FINAL = 'final';
    const STATUS_CANCELED = 'canceled';  // Optional: you can add a canceled status

    const PAYMENT_STATUS_PAID = 'paid';
    const PAYMENT_STATUS_UNPAID = 'unpaid';
    const PAYMENT_STATUS_PARTIAL = 'partial';

    protected $fillable = [
        'customer_id', 'user_id', 'total_items', 'total_amount',
        'discount_type', 'discount_amount', 'tax_amount', 
        'shipping_charges', 'final_total', 'status', 'payment_status',
        'paid_amount', 'sale_date', 'notes', 'created_by'
    ];

    protected static function booted()
    {
        static::creating(function ($sale) {
            if (auth()->check() && !isset($sale->created_by)) {
                $sale->created_by = auth()->id();
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
            
            if (\Schema::hasColumn('sales', 'created_by')) {
                $allowedIds = session('data_scope_user_ids', [$user->id]);
                $builder->whereIn('created_by', $allowedIds);
            }
        });
    }

    protected $casts = [
        'sale_date' => 'datetime', // Cast sale_date to Carbon instance
    ];

    // Sale relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
    
    public function returns()
    {
        return $this->hasMany(SaleReturn::class);
    }

    public function payments()
    {
        return $this->hasMany(SalePayment::class);
    }

    public function salePayments()
    {
        return $this->hasMany(SalePayment::class);
    }

    // Method to check if sale is complete
    public function isCompleted()
    {
        return $this->status === self::STATUS_FINAL;
    }

    // Method to update sale status and handle transitions
    public function setStatus(string $status)
    {
        if (!in_array($status, [self::STATUS_DRAFT, self::STATUS_FINAL, self::STATUS_CANCELED])) {
            throw new \InvalidArgumentException("Invalid status: $status");
        }

        // Ensure that the sale status is only updated to final if it was previously draft
        if ($status === self::STATUS_FINAL && $this->status !== self::STATUS_DRAFT) {
            throw new \Exception('Cannot complete a sale that is not draft.');
        }

        $this->status = $status;
        $this->save();
    }

    // Method to set payment status
    public function setPaymentStatus(string $paymentStatus)
    {
        if (!in_array($paymentStatus, [self::PAYMENT_STATUS_PAID, self::PAYMENT_STATUS_UNPAID, self::PAYMENT_STATUS_PARTIAL])) {
            throw new \InvalidArgumentException("Invalid payment status: $paymentStatus");
        }

        // If the payment is marked as paid, and the sale was draft, update the sale to final
        if ($paymentStatus === self::PAYMENT_STATUS_PAID && $this->status === self::STATUS_DRAFT) {
            $this->setStatus(self::STATUS_FINAL);
        }

        $this->payment_status = $paymentStatus;
        $this->save();
    }

    // Custom method to handle sale completion logic (status, payments, etc.)
    public function completeSale(array $payments)
    {
        $this->setStatus(self::STATUS_FINAL);
        $this->setPaymentStatus(self::PAYMENT_STATUS_PAID);

        // Save payment details
        foreach ($payments as $payment) {
            SalePayment::create([
                'sale_id' => $this->id,
                'amount' => $payment['amount'],
                'payment_method' => $payment['payment_method'],
                'payment_date' => now(),
                'notes' => $payment['notes'],
            ]);
        }

        // Optionally update paid_amount
        $this->paid_amount = array_sum(array_column($payments, 'amount'));
        $this->save();
    }
    
    public function getFinalTotalAttribute()
    {
        return $this->attributes['final_total'] ?? ($this->total_amount + $this->tax_amount - $this->discount_amount);
    }
}
