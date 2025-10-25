<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Sale;
use App\Services\CartService;
use App\Services\LoyaltyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class MobilePOSController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private LoyaltyService $loyaltyService
    ) {
        $this->middleware(['auth:sanctum', 'throttle:200,1']);
    }

    public function getProducts(Request $request)
    {
        $query = Product::with(['category:id,name', 'brand:id,name'])
            ->where('quantity', '>', 0);
            
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('barcode', 'like', "%{$request->search}%");
            });
        }
        
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        
        $products = $query->select([
            'id', 'name', 'sale_price', 'quantity', 'barcode', 
            'category_id', 'brand_id', 'image'
        ])->paginate(20);
        
        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    public function scanBarcode(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string',
        ]);
        
        $product = Product::where('barcode', $request->barcode)
            ->where('quantity', '>', 0)
            ->with(['category:id,name', 'brand:id,name'])
            ->first();
            
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found or out of stock',
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }

    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', Rule::exists('products', 'id')],
            'quantity' => ['required', 'integer', 'min:1', 'max:1000'],
        ]);
        
        $result = $this->cartService->addToCart(
            $validated['product_id'], 
            $validated['quantity']
        );
        
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'integer', Rule::exists('customers', 'id')],
            'payment_method' => ['required', 'string', Rule::in(['cash', 'card', 'mobile_payment'])],
            'paid_amount' => ['required', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
        ]);
        
        try {
            $result = $this->cartService->checkout($validated);
            
            if (!$result['success']) {
                return response()->json($result, 400);
            }
            
            $sale = $result['sale'];
            
            // Process loyalty points if customer provided
            $loyaltyResult = null;
            if ($validated['customer_id']) {
                $loyaltyResult = $this->loyaltyService->processLoyaltyPoints($sale);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'sale' => $sale->load(['customer', 'saleItems.product']),
                    'loyalty' => $loyaltyResult,
                ],
                'message' => 'Sale completed successfully',
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Checkout failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getCustomers(Request $request)
    {
        $query = Customer::query();
        
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }
        
        $customers = $query->select(['id', 'name', 'email', 'phone'])
            ->paginate(20);
            
        return response()->json([
            'success' => true,
            'data' => $customers,
        ]);
    }

    public function getSalesHistory(Request $request)
    {
        $query = Sale::with(['customer:id,name', 'saleItems.product:id,name'])
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at');
            
        if ($request->date_from) {
            $query->whereDate('sale_date', '>=', $request->date_from);
        }
        
        if ($request->date_to) {
            $query->whereDate('sale_date', '<=', $request->date_to);
        }
        
        $sales = $query->paginate(20);
        
        return response()->json([
            'success' => true,
            'data' => $sales,
        ]);
    }
}