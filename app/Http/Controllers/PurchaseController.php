<?php

namespace App\Http\Controllers;

use App\Models\{Purchase, PurchaseItem, Product, PurchaseReturn, PurchaseReturnItem, User, Warehouse};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $purchases = Purchase::with('items.product', 'warehouse')->latest()->paginate(10);
        return view('admin.purchases.index', compact('purchases'));
    }

    public function create()
    {
        $users = User::all();
        $products = Product::all();
        $warehouses = Warehouse::all();
        return view('admin.purchases.create', compact('products', 'users', 'warehouses'));
    }

    public function store(Request $request)
{
    // Validate input
    $validated = $request->validate([
        'date' => 'required|date',
        'user_id' => 'required|exists:users,id',
        'warehouse_id' => 'required|exists:warehouses,id',

        'products' => 'required|array|min:1',
        'products.*.product_id' => 'required|exists:products,id',
        'products.*.quantity' => 'required|numeric|min:1',
        'products.*.product_cost' => 'required|numeric|min:0',
        'products.*.sub_total' => 'required|numeric|min:0',

        'grand_total' => 'required|numeric|min:0',
        'notes' => 'nullable|string',
        'discount' => 'nullable|numeric|min:0',
        'tax_rate' => 'nullable|numeric|min:0',
        'tax_amount' => 'nullable|numeric|min:0',
        'shipping' => 'nullable|numeric|min:0',
        'received_amount' => 'nullable|numeric|min:0',
        'paid_amount' => 'nullable|numeric|min:0',
        'payment_type' => 'nullable|string|in:cash,card,bank,other',
    ]);
    
    DB::beginTransaction();

    try {
        // Create purchase
        $purchase = Purchase::create([
            'user_id' => $request->user_id,
            'date' => $request->date,
            'warehouse_id' => $request->warehouse_id,
            'tax_rate' => $request->tax_rate ?? 0,
            'tax_amount' => $request->tax_amount ?? 0,
            'discount' => $request->discount ?? 0,
            'shipping' => $request->shipping ?? 0,
            'grand_total' => $request->grand_total,
            'received_amount' => $request->received_amount ?? 0,
            'paid_amount' => $request->paid_amount ?? 0,
            'payment_type' => $request->payment_type ?? 'cash',
            'status' => 'received',
            'payment_status' => 'unpaid',
            'notes' => $request->notes,
            'reference_code' => 'PO-' . now()->format('YmdHis'),
        ]);

        // Store purchase items
        foreach ($request->products as $item) {
            $product = Product::findOrFail($item['product_id']);

            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'product_id' => $product->id,
                'product_cost' => $item['product_cost'],
                'net_unit_cost' => $item['product_cost'],
                'tax_type' => $item['tax_type'] ?? null,
                'tax_value' => $item['tax_value'] ?? 0,
                'tax_amount' => $item['tax_amount'] ?? 0,
                'discount_type' => $item['discount_type'] ?? null,
                'discount_value' => $item['discount_value'] ?? 0,
                'discount_amount' => $item['discount_amount'] ?? 0,
                'purchase_unit' => $item['purchase_unit'] ?? null,
                'quantity' => $item['quantity'],
                'sub_total' => $item['sub_total'],
            ]);

            // Update stock
            $product->increment('quantity', $item['quantity']);
        }

        DB::commit();
        return redirect()->route('admin.purchases.index')->with('success', 'Purchase created successfully!');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withErrors(['error' => 'Error: ' . $e->getMessage()])->withInput();
    }
}
     


    public function show($id)
    {
        $purchase = Purchase::with(['items.product', 'warehouse'])->findOrFail($id);
        return view('admin.purchases.show', compact('purchase'));
    }

    public function destroy($id)
    {
        $purchase = Purchase::findOrFail($id);
        $purchase->delete();
        return redirect()->route('admin.purchases.index')->with('success', 'Purchase deleted.');
    }

    public function showReturnForm($purchaseId)
    {
        $purchase = Purchase::with(['items.product', 'warehouse'])->findOrFail($purchaseId);
        return view('admin.purchases.return', compact('purchase'));
    }

    public function processReturn(Request $request, Purchase $purchase)
    {
        $request->validate([
            'return_date' => 'required|date',
            'return_items' => 'required|array|min:1',
            'return_items.*.quantity' => 'required|integer|min:1',
            'return_items.*.sub_total' => 'required|numeric|min:0',
        ]);

        $validItems = array_filter($request->return_items, fn($item) => $item['quantity'] > 0);
        if (empty($validItems)) {
            return back()->withErrors(['error' => 'At least one product must be returned.']);
        }

        DB::beginTransaction();

        try {
            $purchaseReturn = PurchaseReturn::create([
                'purchase_id' => $purchase->id,
                'warehouse_id' => $purchase->warehouse_id ?? 1, // or any default warehouse ID
                'return_date' => $request->return_date,
                'tax_rate' => 0,
                'tax_amount' => 0,
                'discount' => 0,
                'shipping' => 0,
                'grand_total' => 0,
                'received_amount' => 0,
                'paid_amount' => 0,
                'payment_type' => null,
                'status' => 'processed',
                'payment_status' => 'unpaid',
                'notes' => $request->notes,
                'reference_code' => 'RET-' . now()->format('YmdHis'),
            ]);

            $total = 0;

            foreach ($validItems as $itemId => $itemData) {
                $purchaseItem = PurchaseItem::findOrFail($itemId);
                if ($itemData['quantity'] > $purchaseItem->quantity) {
                    throw new \Exception("Return qty exceeds purchased qty for product ID {$purchaseItem->product_id}");
                }

                PurchaseReturnItem::create([
                    'purchase_return_id' => $purchaseReturn->id,
                    'product_id' => $purchaseItem->product_id,
                    'product_cost' => $purchaseItem->product_cost,
                    'net_unit_cost' => $purchaseItem->net_unit_cost,
                    'tax_type' => $purchaseItem->tax_type,
                    'tax_value' => $purchaseItem->tax_value,
                    'tax_amount' => $purchaseItem->tax_amount,
                    'discount_type' => $purchaseItem->discount_type,
                    'discount_value' => $purchaseItem->discount_value,
                    'discount_amount' => $purchaseItem->discount_amount,
                    'purchase_unit' => $purchaseItem->purchase_unit,
                    'quantity' => $itemData['quantity'],
                    'sub_total' => $itemData['sub_total'],
                ]);

                $purchaseItem->decrement('quantity', $itemData['quantity']);
                // Validate stock before decrementing
                if ($purchaseItem->product->quantity >= $itemData['quantity']) {
                    $purchaseItem->product->decrement('quantity', $itemData['quantity']);
                } else {
                    throw new \Exception("Insufficient stock for product: {$purchaseItem->product->name}");
                }

                $total += $itemData['sub_total'];
            }

            $purchaseReturn->update(['grand_total' => $total]);

            DB::commit();
            return redirect()->route('admin.purchases.index')->with('success', 'Purchase return completed!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Return failed: ' . $e->getMessage()]);
        }
    }
}
