@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>📊 Balance Sheet</h4>
                <small class="text-muted">As of {{ date('F d, Y') }}</small>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Assets</h5>
                        <table class="table table-sm">
                            <tr><td>Cash</td><td class="text-right">$5,000.00</td></tr>
                            <tr><td>Accounts Receivable</td><td class="text-right">$2,500.00</td></tr>
                            <tr><td>Inventory</td><td class="text-right">$8,000.00</td></tr>
                            <tr><td>Equipment</td><td class="text-right">$10,000.00</td></tr>
                            <tr class="font-weight-bold"><td>Total Assets</td><td class="text-right">$25,500.00</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Liabilities & Equity</h5>
                        <table class="table table-sm">
                            <tr><td>Accounts Payable</td><td class="text-right">$1,200.00</td></tr>
                            <tr><td>Short-term Loans</td><td class="text-right">$3,000.00</td></tr>
                            <tr><td>Owner's Equity</td><td class="text-right">$21,300.00</td></tr>
                            <tr class="font-weight-bold"><td>Total Liabilities & Equity</td><td class="text-right">$25,500.00</td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection