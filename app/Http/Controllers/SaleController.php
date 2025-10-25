<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\User;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Notifications\SaleCreateNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class SaleController extends Controller
{
    // Use the same structure as your Product controller
    public function __construct()
    {
        // Add middleware to ensure only authenticated users can access sales
        $this->middleware('auth');
    }

    // Show create sale form
    public function create()
    {
        $customers = Customer::all();
        $users = User::all();
        $products = Product::all(); // Add this line to fetch products
        return view('admin.sales.create', compact('customers', 'users', 'products')); // Pass products to the view
    }

    // Store a new sale
  

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'user_id' => 'required|exists:users,id',
            'total_amount' => 'required|numeric|min:0',
            'final_total' => 'required|numeric|min:0',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.total' => 'required|numeric|min:0',
            'status' => 'nullable|in:draft,final',
            'payment_status' => 'nullable|in:paid,unpaid,partial'
        ]);

        DB::beginTransaction();

        try {
            // Check stock availability first
            foreach ($validated['products'] as $item) {
                $product = Product::find($item['product_id']);
                if (!$product || $product->quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: " . ($product ? $product->name : 'Unknown') . ". Available: " . ($product ? $product->quantity : 0));
                }
            }

            // Create Sale
            $sale = Sale::create([
                'customer_id'       => $validated['customer_id'] ?? null,
                'user_id'           => $validated['user_id'],
                'total_items'       => count($validated['products']),
                'total_amount'      => $validated['total_amount'],
                'discount_type'     => $request->discount_type,
                'discount_amount'   => $request->discount_amount ?: 0,
                'tax_amount'        => $request->tax_amount ?: 0,
                'shipping_charges'  => $request->shipping_charges ?: 0,
                'final_total'       => $validated['final_total'],
                'status'            => isset($validated['status']) ? $validated['status'] : 'final',
                'payment_status'    => isset($validated['payment_status']) ? $validated['payment_status'] : 'unpaid',
                'paid_amount'       => $request->paid_amount ?: 0,
                'sale_date'         => now(),
                'notes'             => $request->notes,
            ]);

            // Create Sale Items and update stock
            foreach ($validated['products'] as $item) {
                $product = Product::find($item['product_id']);

                SaleItem::create([
                    'sale_id'           => $sale->id,
                    'product_id'        => $product->id,
                    'product_price'     => $product->sale_price ?: ($product->price ?: 0),
                    'quantity'          => $item['quantity'],
                    'tax_percent'       => isset($item['tax_percent']) ? $item['tax_percent'] : 0,
                    'tax_amount'        => isset($item['tax_amount']) ? $item['tax_amount'] : 0,
                    'discount_percent'  => isset($item['discount_percent']) ? $item['discount_percent'] : 0,
                    'discount_amount'   => isset($item['discount_amount']) ? $item['discount_amount'] : 0,
                    'total'             => $item['total'],
                ]);

                // Update stock
                $product->decrement('quantity', $item['quantity']);
            }

            // Notify users
            try {
                Notification::send(User::all(), new SaleCreateNotification($sale));
            } catch (\Exception $e) {
                // Log notification error but don't fail the sale
                \Log::warning('Failed to send sale notification: ' . $e->getMessage());
            }

            DB::commit();

            return redirect()->route('admin.sales.index')->with('success', 'Sale created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Something went wrong: ' . $e->getMessage()])->withInput();
        }
    }



    // Display a list of sales
    public function index()
    {
        $sales = Sale::with('customer', 'user')->latest()->paginate(10);
        return view('admin.sales.index', compact('sales'));
    }

    // Display the sale details
    public function show($id)
    {
        $sale = Sale::with(['customer', 'user', 'saleItems.product', 'salePayments'])->findOrFail($id);
        return view('admin.sales.show', compact('sale'));
    }

    // Display receipt
    public function receipt($id)
    {
        $sale = Sale::with(['customer', 'user', 'saleItems.product', 'salePayments'])->findOrFail($id);
        return view('admin.sales.receipt', compact('sale'));
    }


    // Edit the sale
    public function edit($id)
    {
        $sale = Sale::findOrFail($id);
        $customers = Customer::all();
        $users = User::all();
        return view('admin.sales.edit', compact('sale', 'customers', 'users'));
    }

    // Update the sale
    public function update(Request $request, $id)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'user_id' => 'required|exists:users,id',
            'total_amount' => 'required|numeric',
            'final_total' => 'required|numeric',
        ]);

        $sale = Sale::findOrFail($id);
        $sale->update([
            'customer_id' => $request->customer_id,
            'user_id' => $request->user_id,
            'total_items' => $request->total_items,
            'total_amount' => $request->total_amount,
            'discount_type' => $request->discount_type,
            'discount_amount' => $request->discount_amount,
            'tax_amount' => $request->tax_amount,
            'shipping_charges' => $request->shipping_charges,
            'final_total' => $request->final_total,
            'status' => $request->status,
            'payment_status' => $request->payment_status,
            'paid_amount' => $request->paid_amount,
            'sale_date' => $request->sale_date,
            'notes' => $request->notes,
        ]);

        return redirect()->route('sales.index')->with('success', 'Sale updated successfully!');
    }

    // Delete a sale
    public function destroy($id)
    {
        $sale = Sale::findOrFail($id);
        $sale->delete();
        return redirect()->route('admin.sales.index')->with('success', 'Sale deleted successfully!');
    }

    // Custom helper method to calculate the final total (similar to your Product logic)
    public static function calculateFinalTotal($sale)
    {
        $total = $sale->total_amount;
        $discount = $sale->discount_amount;
        $tax = $sale->tax_amount;
        $shipping = $sale->shipping_charges;

        // Final total logic can be more complex depending on your business rules
        return $total - $discount + $tax + $shipping;
    }
    public function showReturnForm($saleId)
    {
        $sale = Sale::with(['saleItems.product', 'customer'])->findOrFail($saleId);
        return view('admin.sales.return', compact('sale'));
    }

    
    public function processReturn(Request $request, Sale $sale)
{
    // Validate input
    $request->validate([
        'return_items' => 'required|array',
        'return_items.*.quantity' => 'required|integer|min:0',
        'return_items.*.total' => 'required|numeric|min:0',
        'reason' => 'required|string',
        'return_date' => 'required|date',
    ]);

    $returnItems = $request->input('return_items');
    $totalReturnAmount = 0;

    // Remove items with zero quantity return (user not returning them)
    $returnItems = array_filter($returnItems, fn($item) => $item['quantity'] > 0);

    if (empty($returnItems)) {
        return back()->withErrors(['error' => 'You must return at least one product.'])->withInput();
    }

    DB::beginTransaction();

    try {
        // 1. Create the SaleReturn record
        $saleReturn = SaleReturn::create([
            'sale_id' => $sale->id,
            'customer_id' => $sale->customer_id,
            'user_id' => auth()->id(),
            'reason' => $request->reason,
            'total_amount' => 0,   // update later
            'final_total' => 0,    // update later
            'return_date' => $request->return_date,
        ]);

        // 2. Loop through each return item
        foreach ($returnItems as $saleItemId => $itemData) {
            $quantityToReturn = $itemData['quantity'];

            // Get original sale item
            $saleItem = $sale->saleItems()->findOrFail($saleItemId);

            // Validate return quantity
            if ($quantityToReturn > $saleItem->quantity) {
                throw new \Exception("Return quantity cannot be greater than sold quantity for product ID {$saleItem->product_id}");
            }

            // Calculate total return price for this item
            $returnTotal = $itemData['total'];
            $totalReturnAmount += $returnTotal;

            // 3. Create SaleReturnItem record
            SaleReturnItem::create([
                'sale_return_id' => $saleReturn->id,
                'product_id' => $saleItem->product_id,
                'quantity' => $quantityToReturn,
                'total' => $returnTotal,
            ]);

            // 4. Update the sale item quantity to reflect return
            $saleItem->quantity -= $quantityToReturn;
            $saleItem->save();

            // 5. Update product stock (increase stock because of returned items)
            $saleItem->product->increment('quantity', $quantityToReturn);
        }

        // 6. Update the totals in saleReturn record
        $saleReturn->total_amount = $totalReturnAmount;
        $saleReturn->final_total = $totalReturnAmount; // can adjust if discount or tax applies
        $saleReturn->save();
        

        DB::commit();

        return redirect()->route('admin.sales.index')->with('success', 'Sale return processed successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withErrors(['error' => 'Return failed: ' . $e->getMessage()])->withInput();
    }
    
}
}
