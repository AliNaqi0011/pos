<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\QuotationCreated;
use App\Notifications\QuotationApproved;
use App\Notifications\QuotationRejected;

class QuotationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
$quotations = Quotation::with('customer') // eager load to avoid N+1
        ->orderBy('id', 'desc')
        ->paginate(10); // paginate instead of get()

        return view('admin.quotations.index', compact('quotations'));
    }

    public function create()
    {
        $customers = Customer::all();
        $products = Product::all();
        return view('admin.quotations.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'quotation_date' => 'required|date',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'discount' => 'nullable|numeric',
        ]);

        $subtotal = 0;
        $discount = $validated['discount'] ?? 0;

        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            if (!$product || $product->sale_price === null) {
                return back()->withErrors(['Product with ID ' . $item['product_id'] . ' not found or price is missing.'])->withInput();
            }
            $itemTotal = $product->sale_price * $item['quantity'];
            $subtotal += $itemTotal;
        }

        $tax = $subtotal * 0.1; // Example 10% tax
        $total = $subtotal - $discount + $tax;

        $quotation = Quotation::create([
            'customer_id' => $validated['customer_id'],
            'quotation_date' => $validated['quotation_date'],
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'total' => $total,
            'status' => 'pending',
        ]);

        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            if (!$product || $product->sale_price === null) {
                continue; // skip invalid item
            }

            $itemTotal = $product->sale_price * $item['quantity'];

            QuotationItem::create([
                'quotation_id' => $quotation->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $product->sale_price,
                'total' => $itemTotal,
            ]);
        }

        Notification::send($quotation->customer, new QuotationCreated($quotation));

        return redirect()->route('quotations.show', $quotation->id)
            ->with('success', 'Quotation created successfully.');
    }

    public function show($id)
    {
        $quotation = Quotation::with('items.product')->findOrFail($id);
        return view('admin.quotations.show', compact('quotation'));
    }

    public function edit($id)
    {
        $quotation = Quotation::findOrFail($id);
        $customers = Customer::all();
        $products = Product::all();
        return view('admin.quotations.edit', compact('quotation', 'customers', 'products'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'quotation_date' => 'required|date',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'discount' => 'nullable|numeric',
        ]);

        $subtotal = 0;
        $discount = $validated['discount'] ?? 0;

        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            if (!$product || $product->sale_price === null) {
                return back()->withErrors(['Product with ID ' . $item['product_id'] . ' not found or price is missing.'])->withInput();
            }
            $itemTotal = $product->sale_price * $item['quantity'];
            $subtotal += $itemTotal;
        }

        $tax = $subtotal * 0.1;
        $total = $subtotal - $discount + $tax;

        $quotation = Quotation::findOrFail($id);
        $quotation->update([
            'customer_id' => $validated['customer_id'],
            'quotation_date' => $validated['quotation_date'],
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'total' => $total,
            'status' => 'pending',
        ]);

        $quotation->items()->delete();

        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            if (!$product || $product->sale_price === null) {
                continue;
            }

            $itemTotal = $product->sale_price * $item['quantity'];

            QuotationItem::create([
                'quotation_id' => $quotation->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $product->sale_price,
                'total' => $itemTotal,
            ]);
        }

        Notification::send($quotation->customer, new QuotationCreated($quotation));

        return redirect()->route('quotations.show', $quotation->id)
            ->with('success', 'Quotation updated successfully.');
    }

    public function destroy($id)
    {
        $quotation = Quotation::findOrFail($id);
        $quotation->delete();

        return redirect()->route('quotations.index')
            ->with('success', 'Quotation deleted successfully.');
    }

    public function approve($id)
    {
        $quotation = Quotation::findOrFail($id);
        $quotation->status = 'approved';
        $quotation->save();

        Notification::send($quotation->customer, new QuotationApproved($quotation));

        return redirect()->route('quotations.show', $quotation->id)
            ->with('success', 'Quotation approved successfully.');
    }

    public function reject($id)
    {
        $quotation = Quotation::findOrFail($id);
        $quotation->status = 'rejected';
        $quotation->save();

        Notification::send($quotation->customer, new QuotationRejected($quotation));

        return redirect()->route('quotations.show', $quotation->id)
            ->with('error', 'Quotation rejected.');
    }
}
