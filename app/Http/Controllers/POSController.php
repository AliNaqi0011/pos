<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Warehouse;
use App\Models\Customer;
use App\Models\User;
// Services will be loaded dynamically if available
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SaleCreateNotification;
use Illuminate\Validation\Rule;

class POSController extends Controller
{
    private $cartService;

    public function __construct()
    {
        // Initialize services only if they exist
        if (class_exists('App\Services\CartService')) {
            $this->cartService = app('App\Services\CartService');
        }
    }

    public function index()
    {
        try {
            $categories = Category::select('id', 'name')->get() ?? collect();
            $brands = Brand::select('id', 'name')->get() ?? collect();
            $warehouses = Warehouse::select('id', 'name')->get() ?? collect();
            $products = Product::select('id', 'name', 'sale_price', 'quantity', 'category_id', 'brand_id')->get() ?? collect();
            $customers = Customer::select('id', 'name', 'email')->get() ?? collect();

            return view('admin.pos.infy-pos', compact('categories', 'brands', 'warehouses', 'products', 'customers'));
        } catch (\Exception $e) {
            \Log::error('POS Index Error: ' . $e->getMessage());
            return redirect('/dashboard')->with('error', 'POS system temporarily unavailable. Please try again.');
        }
    }

    public function filterProducts(Request $request)
    {
        $request->validate([
            'category_id' => 'nullable|integer|exists:categories,id',
            'brand_id' => 'nullable|integer|exists:brands,id',
            'warehouse_id' => 'nullable|integer|exists:warehouses,id'
        ]);
        
        $query = Product::query();

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->validated()['category_id']);
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->validated()['brand_id']);
        }

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->validated()['warehouse_id']);
        }

        $products = $query->get();
        return response()->json($products);
    }

    public function addToCart(Request $request)
    {
        $productId = $request->input('product_id') ?: $request->input('id');
        
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:1000'],
        ]);
        
        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 400);
        }
        
        if ($product->quantity < $validated['quantity']) {
            return response()->json(['success' => false, 'message' => "Insufficient stock. Available: {$product->quantity}"], 400);
        }

        return response()->json(['success' => true, 'message' => 'Product added to cart']);
    }
    

    public function checkout(Request $request)
    {
        try {
            // Validate request - STRICT customer validation to prevent auto-creation
            $validated = $request->validate([
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'customer_id' => 'nullable|exists:customers,id',
                'total_amount' => 'required|numeric|min:0',
                'payment_method' => 'required|string|in:cash,card,bank_transfer,mobile'
            ]);
            
            $items = $validated['items'];
            
            // STRICT customer handling - only existing customers or null (walk-in)
            $customerId = null;
            if (!empty($validated['customer_id']) && 
                $validated['customer_id'] !== 'null' && 
                $validated['customer_id'] !== '0' && 
                $validated['customer_id'] !== '' &&
                is_numeric($validated['customer_id'])) {
                
                // Double-check customer exists in database
                $customer = Customer::find($validated['customer_id']);
                if (!$customer) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Selected customer not found. Please select a valid customer or use walk-in option.'
                    ], 400);
                }
                $customerId = $customer->id;
            }
            
            // Log for debugging - remove in production
            \Log::info('POS Checkout - Customer ID processed', [
                'original_customer_id' => $validated['customer_id'] ?? 'null',
                'processed_customer_id' => $customerId,
                'customer_exists' => $customerId ? Customer::find($customerId) !== null : 'walk-in',
                'total_customers_before' => Customer::count()
            ]);
            $totalAmount = $validated['total_amount'];
            $paymentMethod = $validated['payment_method'];
            
            // Check stock availability
            foreach ($items as $item) {
                $product = Product::find($item['product_id']);
                if (!$product || $product->quantity < $item['quantity']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for product: " . ($product ? $product->name : 'Unknown') . ". Available: " . ($product ? $product->quantity : 0)
                    ], 400);
                }
            }

            \DB::beginTransaction();

            // Create sale record
            $sale = Sale::create([
                'user_id' => Auth::id(),
                'customer_id' => $customerId,
                'sale_date' => now(),
                'total_items' => count($items),
                'total_amount' => $totalAmount,
                'discount_amount' => $request->input('discount_amount', 0),
                'tax_amount' => $request->input('tax_amount', 0),
                'final_total' => $totalAmount,
                'paid_amount' => $totalAmount,
                'status' => 'final',
                'payment_status' => 'paid',
            ]);

            // Create sale items and update stock
            $receiptItems = [];
            foreach ($items as $item) {
                $product = Product::find($item['product_id']);
                
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'product_price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total' => $item['price'] * $item['quantity'],
                ]);
                
                // Update product stock
                $product->quantity -= $item['quantity'];
                $product->save();
                
                $receiptItems[] = [
                    'product_id' => $item['product_id'],
                    'product_name' => $product->name,
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total' => $item['price'] * $item['quantity']
                ];
            }

            // Create payment record
            SalePayment::create([
                'sale_id' => $sale->id,
                'amount' => $totalAmount,
                'payment_method' => $paymentMethod,
                'payment_date' => now(),
                'notes' => 'POS checkout payment',
            ]);

            \DB::commit();
            
            // Log final customer count to ensure no auto-creation occurred
            \Log::info('POS Checkout - Sale completed', [
                'sale_id' => $sale->id,
                'customer_id' => $customerId,
                'total_customers_after' => Customer::count()
            ]);

            // Generate receipt data
            $receiptData = [
                'sale_id' => $sale->id,
                'date' => $sale->sale_date->format('Y-m-d H:i:s'),
                'customer' => $customerId ? Customer::find($customerId)->name : 'Walk-in Customer',
                'items' => $receiptItems,
                'subtotal' => $totalAmount - $request->input('tax_amount', 0),
                'discount' => $request->input('discount_amount', 0),
                'tax' => $request->input('tax_amount', 0),
                'total' => $totalAmount,
                'payment_method' => $paymentMethod
            ];

            return response()->json([
                'success' => true,
                'message' => 'Sale completed successfully!',
                'sale_id' => $sale->id,
                'receipt' => $receiptData,
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Checkout failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Checkout failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function salesList()
    {
        $sales = Sale::with('customer', 'items.product')->get();
        return view('admin.sales.index', compact('sales'));
    }

    public function saleDetails($saleId)
    {
        $sale = Sale::with('items.product')->find($saleId);
        return response()->json($sale);
    }
}
