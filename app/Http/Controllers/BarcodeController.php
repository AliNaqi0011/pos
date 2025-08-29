<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class BarcodeController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('admin.barcode.index', compact('products'));
    }

    public function print(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1|max:100',
        ]);

        $product = Product::findOrFail($request->product_id);
        $quantity = $request->quantity;

        return view('admin.barcode.print', compact('product', 'quantity'));
    }
    
    public function scanProduct(Request $request)
    {
        $barcode = $request->get('barcode');
        $product = Product::where('barcode', $barcode)->first();
        
        if ($product) {
            return response()->json([
                'success' => true,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'barcode' => $product->barcode,
                    'sale_price' => $product->sale_price,
                    'quantity' => $product->quantity,
                    'image' => $product->image ? asset('storage/' . $product->image) : null
                ]
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Product not found'
        ]);
    }
}
