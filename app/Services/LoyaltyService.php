<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Sale;
use App\Models\LoyaltyProgram;
use App\Models\CustomerLoyaltyPoints;
use App\Models\PointTransaction;
use App\Models\Promotion;
use App\Models\GiftCard;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LoyaltyService
{
    public function processLoyaltyPoints(Sale $sale): array
    {
        $customer = $sale->customer;
        $loyaltyProgram = LoyaltyProgram::where('is_active', true)
            ->where('tenant_id', auth()->user()->tenant_id)
            ->first();
            
        if (!$loyaltyProgram || !$customer) {
            return ['points_earned' => 0];
        }
        
        return DB::transaction(function () use ($sale, $customer, $loyaltyProgram) {
            $pointsEarned = floor($sale->final_total * $loyaltyProgram->points_per_currency);
            
            // Get or create customer loyalty record
            $customerLoyalty = CustomerLoyaltyPoints::firstOrCreate([
                'customer_id' => $customer->id,
                'loyalty_program_id' => $loyaltyProgram->id,
                'tenant_id' => auth()->user()->tenant_id,
            ], [
                'points_earned' => 0,
                'points_redeemed' => 0,
                'points_balance' => 0,
                'last_activity' => now(),
            ]);
            
            // Update points
            $customerLoyalty->increment('points_earned', $pointsEarned);
            $customerLoyalty->increment('points_balance', $pointsEarned);
            $customerLoyalty->update(['last_activity' => now()]);
            
            // Record transaction
            PointTransaction::create([
                'customer_id' => $customer->id,
                'type' => 'earned',
                'points' => $pointsEarned,
                'reference_type' => 'sale',
                'reference_id' => $sale->id,
                'description' => "Points earned from sale #{$sale->id}",
                'expiry_date' => now()->addYear(),
                'tenant_id' => auth()->user()->tenant_id,
            ]);
            
            return [
                'points_earned' => $pointsEarned,
                'total_points' => $customerLoyalty->points_balance,
            ];
        });
    }

    public function redeemPoints(Customer $customer, int $points, Sale $sale): array
    {
        $loyaltyProgram = LoyaltyProgram::where('is_active', true)
            ->where('tenant_id', auth()->user()->tenant_id)
            ->first();
            
        if (!$loyaltyProgram) {
            throw new \Exception('No active loyalty program found');
        }
        
        if ($points < $loyaltyProgram->min_points_redeem) {
            throw new \Exception("Minimum {$loyaltyProgram->min_points_redeem} points required for redemption");
        }
        
        return DB::transaction(function () use ($customer, $points, $sale, $loyaltyProgram) {
            $customerLoyalty = CustomerLoyaltyPoints::where('customer_id', $customer->id)
                ->where('loyalty_program_id', $loyaltyProgram->id)
                ->lockForUpdate()
                ->first();
                
            if (!$customerLoyalty || $customerLoyalty->points_balance < $points) {
                throw new \Exception('Insufficient points balance');
            }
            
            $redeemValue = $points * $loyaltyProgram->currency_per_point;
            
            // Update customer points
            $customerLoyalty->increment('points_redeemed', $points);
            $customerLoyalty->decrement('points_balance', $points);
            $customerLoyalty->update(['last_activity' => now()]);
            
            // Record transaction
            PointTransaction::create([
                'customer_id' => $customer->id,
                'type' => 'redeemed',
                'points' => $points,
                'reference_type' => 'sale',
                'reference_id' => $sale->id,
                'description' => "Points redeemed for sale #{$sale->id}",
                'tenant_id' => auth()->user()->tenant_id,
            ]);
            
            return [
                'points_redeemed' => $points,
                'redemption_value' => $redeemValue,
                'remaining_points' => $customerLoyalty->points_balance,
            ];
        });
    }

    public function applyPromotion(string $promoCode, Sale $sale): array
    {
        $promotion = Promotion::where('code', $promoCode)
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where('tenant_id', auth()->user()->tenant_id)
            ->first();
            
        if (!$promotion) {
            throw new \Exception('Invalid or expired promotion code');
        }
        
        if ($promotion->usage_limit && $promotion->usage_count >= $promotion->usage_limit) {
            throw new \Exception('Promotion usage limit exceeded');
        }
        
        if ($sale->total_amount < $promotion->min_purchase_amount) {
            throw new \Exception("Minimum purchase amount of {$promotion->min_purchase_amount} required");
        }
        
        return DB::transaction(function () use ($promotion, $sale) {
            $discount = $this->calculatePromotionDiscount($promotion, $sale);
            
            // Update promotion usage
            $promotion->increment('usage_count');
            
            return [
                'promotion_id' => $promotion->id,
                'promotion_name' => $promotion->name,
                'discount_type' => $promotion->type,
                'discount_amount' => $discount,
                'original_amount' => $sale->total_amount,
                'final_amount' => $sale->total_amount - $discount,
            ];
        });
    }

    public function createGiftCard(array $data): GiftCard
    {
        return DB::transaction(function () use ($data) {
            $code = $this->generateGiftCardCode();
            
            return GiftCard::create([
                'code' => $code,
                'initial_value' => $data['value'],
                'current_balance' => $data['value'],
                'customer_id' => $data['customer_id'] ?? null,
                'expiry_date' => $data['expiry_date'] ?? null,
                'status' => 'active',
                'issued_by' => auth()->id(),
                'tenant_id' => auth()->user()->tenant_id,
            ]);
        });
    }

    public function redeemGiftCard(string $code, float $amount): array
    {
        return DB::transaction(function () use ($code, $amount) {
            $giftCard = GiftCard::where('code', $code)
                ->where('status', 'active')
                ->where('tenant_id', auth()->user()->tenant_id)
                ->lockForUpdate()
                ->first();
                
            if (!$giftCard) {
                throw new \Exception('Invalid gift card code');
            }
            
            if ($giftCard->expiry_date && $giftCard->expiry_date < now()) {
                throw new \Exception('Gift card has expired');
            }
            
            if ($giftCard->current_balance < $amount) {
                throw new \Exception('Insufficient gift card balance');
            }
            
            $giftCard->decrement('current_balance', $amount);
            
            if ($giftCard->current_balance <= 0) {
                $giftCard->update(['status' => 'used']);
            }
            
            return [
                'redeemed_amount' => $amount,
                'remaining_balance' => $giftCard->current_balance,
                'status' => $giftCard->status,
            ];
        });
    }

    public function getCustomerLoyaltyStatus(Customer $customer): array
    {
        $loyaltyProgram = LoyaltyProgram::where('is_active', true)
            ->where('tenant_id', auth()->user()->tenant_id)
            ->first();
            
        if (!$loyaltyProgram) {
            return ['status' => 'no_program'];
        }
        
        $customerLoyalty = CustomerLoyaltyPoints::where('customer_id', $customer->id)
            ->where('loyalty_program_id', $loyaltyProgram->id)
            ->first();
            
        if (!$customerLoyalty) {
            return [
                'status' => 'not_enrolled',
                'program_name' => $loyaltyProgram->name,
            ];
        }
        
        $recentTransactions = PointTransaction::where('customer_id', $customer->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
            
        $expiringPoints = PointTransaction::where('customer_id', $customer->id)
            ->where('type', 'earned')
            ->where('expiry_date', '<=', now()->addDays(30))
            ->where('expiry_date', '>', now())
            ->sum('points');
            
        return [
            'status' => 'enrolled',
            'program_name' => $loyaltyProgram->name,
            'points_balance' => $customerLoyalty->points_balance,
            'points_earned' => $customerLoyalty->points_earned,
            'points_redeemed' => $customerLoyalty->points_redeemed,
            'last_activity' => $customerLoyalty->last_activity,
            'expiring_points' => $expiringPoints,
            'recent_transactions' => $recentTransactions,
            'tier' => $this->calculateCustomerTier($customerLoyalty->points_earned),
        ];
    }

    public function expirePoints(): array
    {
        $expiredTransactions = PointTransaction::where('type', 'earned')
            ->where('expiry_date', '<', now())
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('point_transactions as pt2')
                    ->whereRaw('pt2.customer_id = point_transactions.customer_id')
                    ->whereRaw('pt2.reference_type = "expiry"')
                    ->whereRaw('pt2.reference_id = point_transactions.id');
            })
            ->get();
            
        $totalExpired = 0;
        
        foreach ($expiredTransactions as $transaction) {
            DB::transaction(function () use ($transaction, &$totalExpired) {
                // Create expiry transaction
                PointTransaction::create([
                    'customer_id' => $transaction->customer_id,
                    'type' => 'expired',
                    'points' => $transaction->points,
                    'reference_type' => 'expiry',
                    'reference_id' => $transaction->id,
                    'description' => "Points expired from transaction #{$transaction->id}",
                    'tenant_id' => $transaction->tenant_id,
                ]);
                
                // Update customer balance
                $customerLoyalty = CustomerLoyaltyPoints::where('customer_id', $transaction->customer_id)
                    ->first();
                    
                if ($customerLoyalty) {
                    $customerLoyalty->decrement('points_balance', $transaction->points);
                }
                
                $totalExpired += $transaction->points;
            });
        }
        
        return [
            'expired_transactions' => $expiredTransactions->count(),
            'total_points_expired' => $totalExpired,
        ];
    }

    private function calculatePromotionDiscount(Promotion $promotion, Sale $sale): float
    {
        return match($promotion->type) {
            'percentage' => ($sale->total_amount * $promotion->value) / 100,
            'fixed_amount' => min($promotion->value, $sale->total_amount),
            'buy_x_get_y' => $this->calculateBuyXGetYDiscount($promotion, $sale),
            'free_shipping' => 0, // Would be handled in shipping calculation
            default => 0,
        };
    }

    private function calculateBuyXGetYDiscount(Promotion $promotion, Sale $sale): float
    {
        // Simplified buy X get Y calculation
        // In real implementation, this would be more complex based on promotion rules
        $applicableItems = $sale->saleItems->whereIn('product_id', $promotion->applicable_products ?? []);
        $totalQty = $applicableItems->sum('quantity');
        
        $freeItems = floor($totalQty / ($promotion->value + 1)); // Assuming value is X in "buy X get 1"
        $cheapestPrice = $applicableItems->min('product_price');
        
        return $freeItems * $cheapestPrice;
    }

    private function generateGiftCardCode(): string
    {
        do {
            $code = 'GC' . strtoupper(bin2hex(random_bytes(4)));
        } while (GiftCard::where('code', $code)->exists());
        
        return $code;
    }

    private function calculateCustomerTier(int $totalPoints): string
    {
        if ($totalPoints >= 10000) return 'platinum';
        if ($totalPoints >= 5000) return 'gold';
        if ($totalPoints >= 1000) return 'silver';
        return 'bronze';
    }
}