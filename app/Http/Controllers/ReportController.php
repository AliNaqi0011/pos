<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function salesReport(Request $request)
    {
        $query = Sale::with(['customer', 'items.product']);
        
        // Add date filtering if provided
        if ($request->has('from') && $request->from) {
            $query->whereDate('sale_date', '>=', $request->from);
        }
        if ($request->has('to') && $request->to) {
            $query->whereDate('sale_date', '<=', $request->to);
        }
        
        $sales = $query->latest()->get();
        return view('reports.sales', compact('sales'));
    }

    public function productsReport()
    {
        $products = Product::with(['category', 'brand'])
            ->withCount('saleItems')
            ->get();
        return view('reports.products', compact('products'));
    }

    public function customersReport()
    {
        // Use final_total instead of total to avoid column issues
        $customers = Customer::withCount('sales')
            ->withSum('sales', 'final_total')
            ->get();
        return view('reports.customers', compact('customers'));
    }
}