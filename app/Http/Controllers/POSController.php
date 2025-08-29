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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SaleCreateNotification;

class POSController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $brands = Brand::all();
        $warehouses = Warehouse::all();
        $products = Product::all();
        $customers = Customer::all();

        if (!Session::has('sale_id')) {
            $defaultCustomer = Customer::withoutGlobalScopes()->firstOrCreate(
                ['email' => 'walkin@customer.com'],
                ['name' => 'Walk-in Customer', 'phone' => '0000000000']
            );

            $sale = Sale::create([
                'customer_id' => $defaultCustomer->id,
                'user_id' => Auth::id(),
                'total_items' => 0,
                'total_amount' => 0.00,
                'final_total' => 0.00,
                'status' => Sale::STATUS_DRAFT,
                'payment_status' => Sale::PAYMENT_STATUS_UNPAID,
                'sale_date' => now(),
            ]);
            Session::put('sale_id', $sale->id);
            Log::info('Sale initialized in session with ID: ' . $sale->id);
        }

        return view('admin.pos.infy-pos', compact('categories', 'brands', 'warehouses', 'products', 'customers'));
    }

    public function filterProducts(Request $request)
    {
        $query = Product::query();

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->has('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        $products = $query->get();
        return response()->json($products);
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
    
        $product = Product::find($request->product_id);
        $quantity = $request->quantity;
        $total = $product->sale_price * $quantity;
    
        // Check stock availability before adding to cart
        if ($product->quantity < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock. Available: ' . $product->quantity
            ], 400);
        }
    
        $saleId = session()->get('sale_id');
    
        if (!$saleId) {
            $defaultCustomer = Customer::withoutGlobalScopes()->firstOrCreate(
                ['email' => 'walkin@customer.com'],
                ['name' => 'Walk-in Customer', 'phone' => '0000000000']
            );
    
            $sale = Sale::create([
                'customer_id' => $defaultCustomer->id,
                'user_id' => Auth::id(),
                'total_items' => 0,
                'total_amount' => 0.00,
                'final_total' => 0.00,
                'status' => Sale::STATUS_DRAFT,
                'payment_status' => Sale::PAYMENT_STATUS_UNPAID,
                'sale_date' => now(),
            ]);
    
            session()->put('sale_id', $sale->id);
            $saleId = $sale->id;
        }
    
        // Check if item already exists in cart
        $existingItem = SaleItem::where('sale_id', $saleId)
                                ->where('product_id', $product->id)
                                ->first();
    
        if ($existingItem) {
            $existingItem->quantity += $quantity;
            $existingItem->total = $existingItem->quantity * $existingItem->product_price;
            $existingItem->save();
            $cartItem = $existingItem;
        } else {
            $cartItem = SaleItem::create([
                'sale_id' => $saleId,
                'product_id' => $product->id,
                'product_price' => $product->sale_price,
                'quantity' => $quantity,
                'total' => $total,
            ]);
        }
    
        // Update sale totals
        $sale = Sale::find($saleId);
        $sale->total_items = SaleItem::where('sale_id', $saleId)->count();
        $sale->total_amount = SaleItem::where('sale_id', $saleId)->sum('total');
        $sale->final_total = $sale->total_amount;
        $sale->save();
    
        return response()->json([
            'success' => true,
            'message' => 'Product added to cart',
            'cartItem' => $cartItem,
        ]);
    }
    

    public function checkout(Request $request)
    {
        try {
            Log::info('Checkout initiated', $request->all());

            $request->validate([
                'total_items' => 'required|integer|min:1',
                'total_amount' => 'required|numeric',
                'final_total' => 'required|numeric',
                'items' => 'required|array|min:1',
                'items.*.id' => 'required|integer',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
            ]);

        // Always ensure walk-in customer exists and use it if no customer selected
        $walkInCustomer = Customer::withoutGlobalScopes()->firstOrCreate(
            ['email' => 'walkin@customer.com'],
            ['name' => 'Walk-in Customer', 'phone' => '0000000000']
        );
        
        $customerId = $request->customer_id ?: $walkInCustomer->id;

        $sale = Sale::create([
            'user_id'           => auth()->id(),
            'customer_id'       => $customerId,
            'sale_date'         => now(),
            'total_items'       => $request->total_items,
            'total_amount'      => $request->total_amount,
            'discount_type'     => $request->discount_type,
            'discount_amount'   => $request->discount_amount ?? 0,
            'tax_amount'        => $request->tax_amount ?? 0,
            'final_total'       => $request->final_total,
            'paid_amount'       => $request->paid_amount ?? $request->final_total,
            'status'            => 'final',
            'payment_status'    => 'paid',
        ]);

        foreach ($request->items as $item) {
            $price = $item['price'];
            $quantity = $item['quantity'];
            $total = $price * $quantity;
        
            SaleItem::create([
                'sale_id'       => $sale->id,
                'product_id'    => $item['id'],
                'product_price' => $price,
                'quantity'      => $quantity,
                'total'         => $total,
            ]);
            
            // Update product stock - only deduct once during checkout
            $product = Product::withoutGlobalScopes()->find($item['id']);
            if ($product && $product->quantity >= $quantity) {
                $product->decrement('quantity', $quantity);
                Log::info("Stock updated for product {$item['id']}: -{$quantity}, remaining: {$product->fresh()->quantity}");
            }
        }
    

        // Create payment record
        $paidAmount = $request->paid_amount ?? $request->final_total;
        if ($paidAmount > 0) {
            SalePayment::create([
                'sale_id' => $sale->id,
                'amount' => $paidAmount,
                'payment_method' => $request->payment_method ?? 'cash',
                'payment_date' => now(),
                'notes' => 'POS checkout payment',
            ]);
        }

        // Send notification to all users
        $users = User::all();
        Notification::send($users, new SaleCreateNotification($sale));
        
        // Submit to FBR if enabled
        $fbrService = new \App\Services\FBRService();
        if ($fbrService->isEnabled()) {
            $saleWithRelations = $sale->load('customer', 'items.product');
            $fbrResult = $fbrService->submitInvoice($saleWithRelations->toArray());
            
            if (!$fbrResult['success']) {
                Log::warning('FBR submission failed for sale: ' . $sale->id, $fbrResult);
            }
        }
        
        // Clear session
        session()->forget('sale_id');

            return response()->json([
                'success' => true,
                'message' => 'Sale completed successfully!',
                'sale_id' => $sale->id,
                'redirect' => route('admin.sales.receipt', $sale->id),
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Checkout validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', array_flatten($e->errors()))
            ], 422);
        } catch (\Exception $e) {
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
