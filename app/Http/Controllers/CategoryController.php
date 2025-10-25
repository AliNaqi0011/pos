<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Notifications\CategoryCreateNotification;
use App\Notifications\CategoryUpdateNotification;
use App\Notifications\CategoryDeleteNotification;
use App\Traits\SendsNotifications;

class CategoryController extends Controller
{
    use SendsNotifications;
    public function index(Request $request) {
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $search = $request->get('search', '');
    
        $query = Category::with('parent');
    
        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }
    
        $categories = $query->orderBy($sortBy, $sortOrder)
                            ->paginate(10);
    
        if ($request->ajax()) {
            return response()->json([
                'categories' => $categories->items(),
                'pagination' => (string) $categories->links(),
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
                'current_page' => $categories->currentPage(),
                'per_page' => $categories->perPage(),
                'search' => $search,
            ]);
        }
    
        return view('admin.categories.index', compact('categories', 'sortBy', 'sortOrder', 'search'));
    }
    

    public function create() {
        $parents = Category::whereNull('parent_id')->get();
        return view('admin.categories.create', compact('parents'));
    }

    public function store(Request $request) {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:categories,id'],
        ]);

        $category = Category::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'description' => $request->description,
        ]);

        $this->sendNotificationToAll(CategoryCreateNotification::class, $category);

        return redirect()->route('categories')->with('success', 'Category created successfully!');
    }

    public function edit($id) {
        $category = Category::findOrFail($id);
        $parents = Category::whereNull('parent_id')->where('id', '!=', $id)->get();
        return view('admin.categories.edit', compact('category', 'parents'));
    }

   public function update(Request $request) {
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'parent_id' => ['nullable', 'exists:categories,id'],
    ]);

    $category = Category::findOrFail($request->id);
    $category->update([
        'name' => $request->name,
        'parent_id' => $request->parent_id,
        'description' => $request->description,
    ]);

    $this->sendNotificationToAll(CategoryUpdateNotification::class, $category);

    if ($request->ajax()) {
        return response()->json(['message' => 'Category updated successfully!']);
    }

    return redirect()->route('categories')->with('success', 'Category updated successfully!');
}

    public function delete($id) {
        $category = Category::findOrFail($id);
        $this->sendNotificationToAll(CategoryDeleteNotification::class, $category);
        $category->delete();

        return redirect()->route('categories')->with('success', 'Category deleted successfully!');
    }
}
