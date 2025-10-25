<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class CartService
{
    private ?string $cartKey;

    public function __construct()
    {
        // Cart key will be generated when needed
        $this->cartKey = null;
    }

    public function addToCart(int $productId, int $quantity): array
    {
        return DB::transaction(function () use ($productId, $quantity) {
            // Lock product row to prevent race conditions
            $product = Product::lockForUpdate()->find($productId);
            
            if (!$product) {
                return ['success' => false, 'message' => 'Product not found'];
            }

            if ($product->quantity < $quantity) {
                return ['success' => false, 'message' => "Insufficient stock. Available: {$product->quantity}"];
            }

            $cart = $this->getCart();
            $existingItemKey = collect($cart['items'])->search(fn($item) => $item['product_id'] == $productId);

            if ($existingItemKey !== false) {
                $cart['items'][$existingItemKey]['quantity'] += $quantity;
                $cart['items'][$existingItemKey]['total'] = $cart['items'][$existingItemKey]['quantity'] * $product->sale_price;
            } else {
                $cart['items'][] = [
                    'product_id' => $productId,
                    'product_name' => $product->name,
                    'price' => $product->sale_price,
                    'quantity' => $quantity,
                    'total' => $product->sale_price * $quantity
                ];
            }

            $this->updateCartTotals($cart);
            $this->saveCart($cart);

            return ['success' => true, 'message' => 'Product added to cart', 'cart' => $cart];
        });
    }

    public function checkout(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $cart = $this->getCart();
            
            if (empty($cart['items'])) {
                return ['success' => false, 'message' => 'Cart is empty'];
            }

            // Validate stock for all items
            foreach ($cart['items'] as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);
                if ($product->quantity < $item['quantity']) {
                    return ['success' => false, 'message' => "Insufficient stock for {$product->name}"];
                }
            }

            $customer = $this->getOrCreateCustomer($data['customer_id'] ?? null);
            
            $sale = Sale::create([
                'user_id' => Auth::id(),
                'customer_id' => $customer ? $customer->id : null,
                'sale_date' => now(),
                'total_items' => count($cart['items']),
                'total_amount' => $cart['total_amount'],
                'discount_amount' => $data['discount_amount'] ?? 0,
                'tax_amount' => $data['tax_amount'] ?? 0,
                'final_total' => $cart['final_total'],
                'paid_amount' => $data['paid_amount'] ?? $cart['final_total'],
                'status' => 'final',
                'payment_status' => 'paid',
            ]);

            foreach ($cart['items'] as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'product_price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total' => $item['total'],
                ]);

                // Deduct stock
                Product::where('id', $item['product_id'])->decrement('quantity', $item['quantity']);
            }

            $this->clearCart();

            return ['success' => true, 'sale' => $sale];
        });
    }

    public function addItem(int $productId, int $quantity, float $price): void
    {
        $cart = $this->getCart();
        $cart['items'][] = [
            'product_id' => $productId,
            'quantity' => $quantity,
            'price' => $price
        ];
        $this->saveCart($cart);
    }
    
    public function removeItem(int $productId): void
    {
        $cart = $this->getCart();
        $cart['items'] = array_filter($cart['items'], fn($item) => $item['product_id'] !== $productId);
        $this->saveCart($cart);
    }
    
    public function getTotal(): float
    {
        $cart = $this->getCart();
        return collect($cart['items'])->sum(fn($item) => $item['quantity'] * $item['price']);
    }
    
    private function getCart(): array
    {
        $this->ensureCartKey();
        return Cache::get($this->cartKey, ['items' => [], 'total_amount' => 0, 'final_total' => 0]);
    }

    private function saveCart(array $cart): void
    {
        $this->ensureCartKey();
        Cache::put($this->cartKey, $cart, now()->addHours(24));
    }

    private function updateCartTotals(array &$cart): void
    {
        $cart['total_amount'] = collect($cart['items'])->sum('total');
        $cart['final_total'] = $cart['total_amount'];
    }

    private function clearCart(): void
    {
        $this->ensureCartKey();
        Cache::forget($this->cartKey);
    }

    private function getOrCreateCustomer(?int $customerId): ?Customer
    {
        if ($customerId) {
            return Customer::find($customerId);
        }

        // NEVER auto-create customers - return null for walk-in sales
        return null;
    }
    
    private function ensureCartKey(): void
    {
        if ($this->cartKey === null) {
            $userId = Auth::check() ? Auth::id() : 'guest';
            $sessionId = session()->getId() ?: 'default';
            $this->cartKey = 'cart_' . $userId . '_' . $sessionId;
        }
    }
}