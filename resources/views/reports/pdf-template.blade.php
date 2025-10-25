<!DOCTYPE html>
<html>
<head>
    <title>{{ ucfirst($type) }} Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .company-info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total { font-weight: bold; background-color: #f9f9f9; }
        .summary { margin-top: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ ucfirst($type) }} Report</h1>
        <p>Generated on: {{ date('F d, Y H:i:s') }}</p>
    </div>

    <div class="company-info">
        <h3>Company Information</h3>
        <p><strong>Business Name:</strong> Your POS System</p>
        <p><strong>Report Period:</strong> {{ request('date_range', 'All Time') }}</p>
    </div>

    @if($type === 'sales')
        <h3>Sales Summary</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @php $totalAmount = 0; @endphp
                @foreach($data as $sale)
                <tr>
                    <td>{{ $sale->created_at->format('Y-m-d') }}</td>
                    <td>{{ $sale->customer->name ?? 'Walk-in Customer' }}</td>
                    <td>{{ $sale->total_items }}</td>
                    <td>${{ number_format($sale->final_total, 2) }}</td>
                    <td>{{ ucfirst($sale->status) }}</td>
                </tr>
                @php $totalAmount += $sale->final_total; @endphp
                @endforeach
                <tr class="total">
                    <td colspan="3"><strong>Total</strong></td>
                    <td><strong>${{ number_format($totalAmount, 2) }}</strong></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    @endif

    @if($type === 'customer')
        <h3>Customer Analysis</h3>
        <table>
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Total Orders</th>
                    <th>Total Spent</th>
                    <th>Last Order</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $customerData = $data->groupBy('customer_id')->map(function($sales) {
                        $customer = $sales->first()->customer;
                        return [
                            'customer' => $customer,
                            'total_orders' => $sales->count(),
                            'total_spent' => $sales->sum('final_total'),
                            'last_order' => $sales->max('created_at')
                        ];
                    })->sortByDesc('total_spent');
                @endphp
                @foreach($customerData as $customer)
                <tr>
                    <td>{{ $customer['customer']->name ?? 'Walk-in Customer' }}</td>
                    <td>{{ $customer['customer']->email ?? 'N/A' }}</td>
                    <td>{{ $customer['total_orders'] }}</td>
                    <td>${{ number_format($customer['total_spent'], 2) }}</td>
                    <td>{{ $customer['last_order']->format('Y-m-d') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($type === 'inventory')
        <h3>Inventory Report</h3>
        <p>This report shows product sales performance during the selected period.</p>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity Sold</th>
                    <th>Revenue</th>
                    <th>Avg Price</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $productData = collect();
                    foreach($data as $sale) {
                        foreach($sale->saleItems as $item) {
                            $productId = $item->product_id;
                            if (!$productData->has($productId)) {
                                $productData[$productId] = [
                                    'name' => $item->product->name ?? 'Unknown Product',
                                    'quantity' => 0,
                                    'revenue' => 0
                                ];
                            }
                            $productData[$productId]['quantity'] += $item->quantity;
                            $productData[$productId]['revenue'] += $item->quantity * $item->product_price;
                        }
                    }
                    $productData = $productData->sortByDesc('revenue');
                @endphp
                @foreach($productData as $product)
                <tr>
                    <td>{{ $product['name'] }}</td>
                    <td>{{ $product['quantity'] }}</td>
                    <td>${{ number_format($product['revenue'], 2) }}</td>
                    <td>${{ $product['quantity'] > 0 ? number_format($product['revenue'] / $product['quantity'], 2) : '0.00' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="summary">
        <h3>Report Summary</h3>
        <p><strong>Total Records:</strong> {{ $data->count() }}</p>
        <p><strong>Generated By:</strong> {{ auth()->user()->name }}</p>
        <p><strong>Report Type:</strong> {{ ucfirst($type) }} Analysis</p>
    </div>
</body>
</html>