<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - Sale #{{ $sale->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            background: #fff;
            padding: 20px;
        }

        .receipt {
            max-width: 300px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border: 1px solid #ddd;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .company-info {
            font-size: 10px;
            margin-bottom: 2px;
        }

        .receipt-title {
            font-size: 14px;
            font-weight: bold;
            margin: 15px 0 10px 0;
            text-align: center;
        }

        .receipt-info {
            margin-bottom: 15px;
            font-size: 11px;
        }

        .receipt-info div {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }

        .items-header {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 5px 0;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
        }

        .item {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            border-bottom: 1px dotted #ccc;
        }

        .item-name {
            flex: 1;
            margin-right: 10px;
        }

        .item-qty {
            width: 30px;
            text-align: center;
        }

        .item-price {
            width: 60px;
            text-align: right;
        }

        .totals {
            margin-top: 15px;
            border-top: 1px solid #000;
            padding-top: 10px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .total-row.grand-total {
            font-weight: bold;
            font-size: 14px;
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 10px;
        }

        .payment-info {
            margin-top: 15px;
            border-top: 1px solid #000;
            padding-top: 10px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            border-top: 2px solid #000;
            padding-top: 10px;
            font-size: 10px;
        }

        .thank-you {
            font-weight: bold;
            margin-bottom: 10px;
        }

        @media print {
            body {
                padding: 0;
            }
            .receipt {
                border: none;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <!-- Header -->
        <div class="header">
            <div class="company-name">{{ config('app.name', 'POS System') }}</div>
            <div class="company-info">123 Business Street</div>
            <div class="company-info">City, State 12345</div>
            <div class="company-info">Phone: (555) 123-4567</div>
        </div>

        <!-- Receipt Title -->
        <div class="receipt-title">SALES RECEIPT</div>

        <!-- Receipt Info -->
        <div class="receipt-info">
            <div>
                <span>Receipt #:</span>
                <span>{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div>
                <span>Date:</span>
                <span>{{ $sale->created_at->format('M d, Y H:i') }}</span>
            </div>
            <div>
                <span>Cashier:</span>
                <span>{{ $sale->user->name ?? 'System' }}</span>
            </div>
            <div>
                <span>Customer:</span>
                <span>{{ $sale->customer->name ?? 'Walk-in' }}</span>
            </div>
        </div>

        <!-- Items Header -->
        <div class="items-header">
            <span>Item</span>
            <span>Qty</span>
            <span>Amount</span>
        </div>

        <!-- Items -->
        @foreach($sale->saleItems as $item)
        <div class="item">
            <div class="item-name">{{ $item->product->name ?? 'Product' }}</div>
            <div class="item-qty">{{ $item->quantity }}</div>
            <div class="item-price">${{ number_format($item->total, 2) }}</div>
        </div>
        @endforeach

        <!-- Totals -->
        <div class="totals">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>${{ number_format($sale->total_amount, 2) }}</span>
            </div>
            @if($sale->discount_amount > 0)
            <div class="total-row">
                <span>Discount:</span>
                <span>-${{ number_format($sale->discount_amount, 2) }}</span>
            </div>
            @endif
            @if($sale->tax_amount > 0)
            <div class="total-row">
                <span>Tax:</span>
                <span>${{ number_format($sale->tax_amount, 2) }}</span>
            </div>
            @endif
            <div class="total-row grand-total">
                <span>TOTAL:</span>
                <span>${{ number_format($sale->final_total, 2) }}</span>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="payment-info">
            <div class="total-row">
                <span>Payment Method:</span>
                <span>{{ ucfirst($sale->salePayments->first()->payment_method ?? 'Cash') }}</span>
            </div>
            <div class="total-row">
                <span>Amount Paid:</span>
                <span>${{ number_format($sale->paid_amount, 2) }}</span>
            </div>
            @if($sale->paid_amount > $sale->final_total)
            <div class="total-row">
                <span>Change:</span>
                <span>${{ number_format($sale->paid_amount - $sale->final_total, 2) }}</span>
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="thank-you">Thank You for Your Business!</div>
            <div>Visit us again soon</div>
            <div>{{ config('app.url') }}</div>
        </div>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>