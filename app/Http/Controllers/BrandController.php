<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\User;
use App\Notifications\BrandCreateNotification;
use App\Notifications\BrandUpdateNotification;
use App\Notifications\BrandDeleteNotification;

class BrandController extends Controller
{
    public function index(Request $request)
{
    $sortBy = $request->get('sort_by', 'id');
    $sortOrder = $request->get('sort_order', 'desc');
    $search = $request->get('search', '');

    $query = Brand::query();

    if (!empty($search)) {
        $query->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
    }

    $brands = $query->orderBy($sortBy, $sortOrder)->paginate(10);

    if ($request->ajax()) {
        return response()->json([
            'brands' => $brands->items(),
            'pagination' => (string) $brands->links(),
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
            'current_page' => $brands->currentPage(),
            'per_page' => $brands->perPage(),
            'search' => $search,
        ]);
    }

    return view('admin.brands.index', compact('brands', 'sortBy', 'sortOrder', 'search'));
}


    public function create() {
        return view('admin.brands.create');
    }

    public function store(Request $request) {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $brand = Brand::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // Notify admin or relevant user
        $admin = User::where('role', 'admin')->first(); // Make sure a user has this role
        if ($admin) {
            $admin->notify(new BrandCreateNotification($brand));
        }

        return redirect()->route('brands')->with('success', 'Brand created successfully!');
    }

    public function edit($id) {
        $brand = Brand::findOrFail($id);
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request) {
        $request->validate([
            'id' => ['required', 'exists:brands,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $brand = Brand::findOrFail($request->id);
        $brand->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // Notify admin or relevant user
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $admin->notify(new BrandUpdateNotification($brand));
        }

        return redirect()->route('brands')->with('success', 'Brand updated successfully!');
    }

    public function delete($id) {
        $brand = Brand::findOrFail($id);

        // Notify admin or relevant user
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $admin->notify(new BrandDeleteNotification($brand));
        }

        $brand->delete();

        return redirect()->route('brands')->with('error', 'Brand deleted successfully!');
    }
}
