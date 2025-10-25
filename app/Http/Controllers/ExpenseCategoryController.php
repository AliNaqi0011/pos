<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
use App\Notifications\ExpenseCategoryCreateNotification;
use App\Notifications\ExpenseCategoryUpdateNotification;
use App\Notifications\ExpenseCategoryDeleteNotification;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request)
    {
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $search = $request->get('search', '');

        $query = ExpenseCategory::query();

        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        $expenseCategories = $query->orderBy($sortBy, $sortOrder)->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'categories' => $expenseCategories->items(),
                'pagination' => (string) $expenseCategories->links(),
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
                'current_page' => $expenseCategories->currentPage(),
                'per_page' => $expenseCategories->perPage(),
                'search' => $search,
            ]);
        }

        return view('admin.expense-categories.index', compact('expenseCategories', 'sortBy', 'sortOrder', 'search'));
    }

    public function create()
    {
        return view('admin.expense-categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:expense_categories,name',
            'description' => 'nullable|string',
        ]);

        $category = ExpenseCategory::create($request->only(['name', 'description']));

        $category->notify(new ExpenseCategoryCreateNotification($category));

        return redirect()->route('expense-categories')->with('success', 'Expense Category created successfully!');
    }

    public function edit($id)
    {
        $category = ExpenseCategory::findOrFail($id);
        return view('admin.expense-categories.edit', compact('category'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:expense_categories,id',
            'name' => 'required|string|unique:expense_categories,name,' . $request->id,
            'description' => 'nullable|string',
        ]);

        $category = ExpenseCategory::findOrFail($request->id);
        $category->update($request->only(['name', 'description']));

        $category->notify(new ExpenseCategoryUpdateNotification($category));

        if ($request->ajax()) {
            return response()->json(['message' => 'Expense Category updated successfully!']);
        }

        return redirect()->route('expense-categories')->with('success', 'Expense Category updated successfully!');
    }

    public function delete($id)
    {
        $category = ExpenseCategory::findOrFail($id);

        $category->notify(new ExpenseCategoryDeleteNotification($category));
        
        $category->delete();

        return redirect()->route('expense-categories')->with('success', 'Expense Category deleted successfully!');
    }
}
