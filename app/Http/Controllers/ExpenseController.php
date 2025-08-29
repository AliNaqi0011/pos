<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Warehouse;
use App\Models\ExpenseCategory;
use App\Notifications\ExpenseCreateNotification;
use App\Notifications\ExpenseUpdateNotification;
use App\Notifications\ExpenseDeleteNotification;

class ExpenseController extends Controller
{
    public function index(Request $request) {
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $search = $request->get('search', '');

        $query = Expense::with(['warehouse', 'category']);

        if (!empty($search)) {
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('reference_code', 'like', "%{$search}%")
                  ->orWhere('details', 'like', "%{$search}%");
        }

        $expenses = $query->orderBy($sortBy, $sortOrder)->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'expenses' => $expenses->items(),
                'pagination' => (string) $expenses->links(),
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
                'current_page' => $expenses->currentPage(),
                'per_page' => $expenses->perPage(),
                'search' => $search,
            ]);
        }

        return view('admin.expenses.index', compact('expenses', 'sortBy', 'sortOrder', 'search'));
    }

    public function create() {
        $warehouses = Warehouse::all();
        $categories = ExpenseCategory::all();
        return view('admin.expenses.create', compact('warehouses', 'categories'));
    }

    public function store(Request $request) {
    $request->validate([
        'date' => 'required|date',
        'warehouse_id' => 'required|exists:warehouses,id',
        'expense_category_id' => 'required|exists:expense_categories,id',
        'amount' => 'required|numeric|min:0',
        'title' => 'required|string|max:255',
        // 'details' can be nullable, so no validation needed here
    ]);

    // Generate unique reference code
    do {
        $referenceCode = 'EXP-' . strtoupper(uniqid());
        // Check if reference code already exists (just in case)
        $exists = \App\Models\Expense::where('reference_code', $referenceCode)->exists();
    } while ($exists);

    // Prepare data for insertion
    $data = $request->only([
        'date',
        'warehouse_id',
        'expense_category_id',
        'amount',
        'title',
        'details',
    ]);
    $data['reference_code'] = $referenceCode;

    $expense = \App\Models\Expense::create($data);

    $expense->notify(new \App\Notifications\ExpenseCreateNotification($expense));

    return redirect()->route('expenses')->with('success', 'Expense created successfully!');
}



    public function edit($id) {
        $expense = Expense::findOrFail($id);
        $warehouses = Warehouse::all();
        $categories = ExpenseCategory::all();
        return view('admin.expenses.edit', compact('expense', 'warehouses', 'categories'));
    }

    public function update(Request $request) {
        $request->validate([
            'id' => 'required|exists:expenses,id',
            'date' => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'title' => 'required|string|max:255',
        ]);

        $expense = Expense::findOrFail($request->id);
        $expense->update($request->all());

        $expense->notify(new ExpenseUpdateNotification($expense));

        if ($request->ajax()) {
            return response()->json(['message' => 'Expense updated successfully!']);
        }

        return redirect()->route('expenses')->with('success', 'Expense updated successfully!');
    }

    public function delete($id) {
        $expense = Expense::findOrFail($id);
        $expense->notify(new ExpenseDeleteNotification($expense));
        $expense->delete();

        return redirect()->route('expenses')->with('error', 'Expense deleted successfully!');
    }
}
