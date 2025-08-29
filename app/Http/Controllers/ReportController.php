<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function salesReport(Request $request)
    {
        $period = $request->get('period', 'daily');
        $date = Carbon::parse($request->get('date', now()));

        switch ($period) {
            case 'weekly':
                $startDate = $date->startOfWeek();
                $endDate = $date->endOfWeek();
                break;
            case 'monthly':
                $startDate = $date->startOfMonth();
                $endDate = $date->endOfMonth();
                break;
            default:
                $startDate = $date->startOfDay();
                $endDate = $date->endOfDay();
        }

        $sales = Sale::whereBetween('sale_date', [$startDate, $endDate])
            ->with(['customer', 'saleItems.product'])
            ->get();

        $data = [
            'period' => $period,
            'date' => $date->format('Y-m-d'),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'total_sales' => $sales->count(),
            'total_amount' => $sales->sum('final_total'),
            'sales' => $sales
        ];

        if ($request->get('export') === 'csv') {
            return $this->exportSalesCSV($data);
        }

        return view('admin.reports.sales', $data);
    }

    public function productsReport(Request $request)
    {
        $period = $request->get('period', 'monthly');
        $date = Carbon::parse($request->get('date', now()));

        $products = Product::with(['category', 'brand'])->get();

        $data = [
            'period' => $period,
            'date' => $date->format('Y-m-d'),
            'products' => $products,
            'low_stock' => $products->where('quantity', '<', 10),
            'out_of_stock' => $products->where('quantity', 0)
        ];

        if ($request->get('export') === 'csv') {
            return $this->exportProductsCSV($data);
        }

        return view('admin.reports.products', $data);
    }

    public function customersReport(Request $request)
    {
        $customers = Customer::withCount('sales')
            ->withSum('sales', 'final_total')
            ->get();

        $data = [
            'customers' => $customers,
            'total_customers' => $customers->count(),
            'active_customers' => $customers->where('sales_count', '>', 0)->count()
        ];

        if ($request->get('export') === 'csv') {
            return $this->exportCustomersCSV($data);
        }

        return view('admin.reports.customers', $data);
    }

    private function exportSalesCSV($data)
    {
        $filename = "sales_report_{$data['period']}_{$data['date']}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Sale ID', 'Date', 'Customer', 'Items', 'Total Amount', 'Status']);
            
            foreach ($data['sales'] as $sale) {
                fputcsv($file, [
                    $sale->id,
                    $sale->sale_date->format('Y-m-d H:i'),
                    $sale->customer->name,
                    $sale->total_items,
                    $sale->final_total,
                    $sale->status
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportProductsCSV($data)
    {
        $filename = "products_report_{$data['date']}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Product ID', 'Name', 'Category', 'Brand', 'Stock', 'Sale Price', 'Status']);
            
            foreach ($data['products'] as $product) {
                $status = $product->quantity == 0 ? 'Out of Stock' : ($product->quantity < 10 ? 'Low Stock' : 'In Stock');
                fputcsv($file, [
                    $product->id,
                    $product->name,
                    $product->category->name ?? '',
                    $product->brand->name ?? '',
                    $product->quantity,
                    $product->sale_price,
                    $status
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportCustomersCSV($data)
    {
        $filename = "customers_report_" . now()->format('Y-m-d') . ".csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Customer ID', 'Name', 'Email', 'Phone', 'Total Orders', 'Total Spent']);
            
            foreach ($data['customers'] as $customer) {
                fputcsv($file, [
                    $customer->id,
                    $customer->name,
                    $customer->email,
                    $customer->phone,
                    $customer->sales_count,
                    $customer->sales_sum_final_total ?? 0
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}