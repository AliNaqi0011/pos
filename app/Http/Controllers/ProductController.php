<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ProductCreateNotification;
use App\Notifications\ProductUpdateNotification;
use App\Notifications\ProductDeleteNotification;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'brand', 'warehouse'])->get();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();
        $warehouses = Warehouse::all();
        return view('admin.products.create', compact('categories', 'brands', 'warehouses'));
    }
    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'name'          => 'required|string|max:255',
            'category_id'   => 'required|exists:categories,id',
            'brand_id'      => 'nullable|exists:brands,id',
            'warehouse_id'  => 'nullable|exists:warehouses,id',
            'cost_price'    => 'nullable|numeric',
            'sale_price'    => 'nullable|numeric',
            'discount_price'=> 'nullable|numeric',
            'quantity'      => 'required|integer|min:0',
            'description'   => 'nullable|string',
            'barcode'       => 'nullable|string|unique:products,barcode',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
    
        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = 'product_' . time() . '.' . $image->getClientOriginalExtension();
    
            // Store the image in the 'products' folder under 'public' disk
            $imagePath = $image->storeAs('products', $filename, 'public');
        }
    
        // Log the data being stored
        Log::info('Storing Product:', [
            'name' => $request->name,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'warehouse_id' => $request->warehouse_id,
            'cost_price' => $request->cost_price,
            'sale_price' => $request->sale_price,
            'discount_price' => $request->discount_price,
            'quantity' => $request->quantity,
            'description' => $request->description,
            'barcode' => $request->barcode,
            'image' => $imagePath,
        ]);
    
        // Generate barcode if not provided
        $barcode = $request->barcode;
        if (empty($barcode)) {
            $barcode = 'PRD' . str_pad(Product::count() + 1, 6, '0', STR_PAD_LEFT);
            while (Product::where('barcode', $barcode)->exists()) {
                $barcode = 'PRD' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
            }
        }
        
        // Store the product
        try {
            $product = Product::create([
                'name'           => $request->name,
                'category_id'    => $request->category_id,
                'brand_id'       => $request->brand_id,
                'warehouse_id'   => $request->warehouse_id,
                'cost_price'     => $request->cost_price,
                'sale_price'     => $request->sale_price,
                'discount_price' => $request->discount_price,
                'quantity'       => $request->quantity,
                'description'    => $request->description,
                'barcode'        => $barcode,
                'image'          => $imagePath,
            ]);
    
            // Log successful creation
            Log::info('Product created successfully:', $product->toArray());
            
            // Send notification to all users
            $users = User::all();
            Notification::send($users, new ProductCreateNotification($product));
    
            return redirect()->route('products.index')->with('success', 'Product created successfully!');
        } catch (\Exception $e) {
            // Log the error if it fails
            Log::error('Error creating product:', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to create product.');
        }
    }
    

    

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        $brands = Brand::all();
        $warehouses = Warehouse::all();
        return view('admin.products.edit', compact('product', 'categories', 'brands', 'warehouses'));
    }

    public function update(Request $request, $id = null)
    {
        $productId = $id ?? $request->id;
        
        // Validation
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'category_id'   => 'required|exists:categories,id',
            'brand_id'      => 'nullable|exists:brands,id',
            'warehouse_id'  => 'nullable|exists:warehouses,id',
            'price'         => 'nullable|numeric|min:0',
            'cost_price'    => 'nullable|numeric|min:0',
            'sale_price'    => 'nullable|numeric|min:0',
            'discount_price'=> 'nullable|numeric|min:0',
            'quantity'      => 'required|integer|min:0',
            'description'   => 'nullable|string',
            'barcode'       => 'nullable|string|unique:products,barcode,' . $productId,
            'image'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $product = Product::findOrFail($productId);
        $imagePath = $product->image;

        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $image = $request->file('image');
            $filename = 'product_' . time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('products', $filename, 'public');
        }

        // Update the product
        $product->update([
            'name'           => $validated['name'],
            'category_id'    => $validated['category_id'],
            'brand_id'       => $validated['brand_id'],
            'warehouse_id'   => $validated['warehouse_id'],
            'price'          => $validated['price'] ?? $validated['sale_price'] ?? 0,
            'cost_price'     => $validated['cost_price'],
            'sale_price'     => $validated['sale_price'] ?? $validated['price'] ?? 0,
            'discount_price' => isset($validated['discount_price']) ? $validated['discount_price'] : null,
            'quantity'       => $validated['quantity'],
            'description'    => $validated['description'],
            'barcode'        => $validated['barcode'] ?? $product->barcode,
            'image'          => $imagePath,
        ]);
        
        // Send notification to all users
        try {
            $users = User::all();
            Notification::send($users, new ProductUpdateNotification($product));
        } catch (\Exception $e) {
            \Log::warning('Failed to send product update notification: ' . $e->getMessage());
        }

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }
        
        // Send notification to all users before deleting
        $users = User::all();
        Notification::send($users, new ProductDeleteNotification($product));

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully!');
    }
}
