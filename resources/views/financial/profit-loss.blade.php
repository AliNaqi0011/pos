@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>📈 Profit & Loss Statement</h4>
                <small class="text-muted">For the month of {{ date('F Y') }}</small>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr class="bg-light">
                        <td><strong>Revenue</strong></td>
                        <td></td>
                    </tr>
                    <tr><td>&nbsp;&nbsp;Sales Revenue</td><td class="text-right">$15,000.00</td></tr>
                    <tr><td>&nbsp;&nbsp;Service Revenue</td><td class="text-right">$2,000.00</td></tr>
                    <tr class="font-weight-bold"><td>Total Revenue</td><td class="text-right">$17,000.00</td></tr>
                    
                    <tr class="bg-light">
                        <td><strong>Expenses</strong></td>
                        <td></td>
                    </tr>
                    <tr><td>&nbsp;&nbsp;Cost of Goods Sold</td><td class="text-right">$8,000.00</td></tr>
                    <tr><td>&nbsp;&nbsp;Rent Expense</td><td class="text-right">$1,500.00</td></tr>
                    <tr><td>&nbsp;&nbsp;Utilities</td><td class="text-right">$300.00</td></tr>
                    <tr><td>&nbsp;&nbsp;Marketing</td><td class="text-right">$500.00</td></tr>
                    <tr class="font-weight-bold"><td>Total Expenses</td><td class="text-right">$10,300.00</td></tr>
                    
                    <tr class="bg-success text-white">
                        <td><strong>Net Profit</strong></td>
                        <td class="text-right"><strong>$6,700.00</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection