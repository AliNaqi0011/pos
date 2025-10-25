@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>⚖️ Stock Adjustments</h4>
                <button class="btn btn-primary btn-sm float-right">New Adjustment</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Adjustment ID</th>
                                <th>Product</th>
                                <th>Warehouse</th>
                                <th>Previous Qty</th>
                                <th>Adjusted Qty</th>
                                <th>Difference</th>
                                <th>Reason</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>ADJ001</td>
                                <td>iPhone 15</td>
                                <td>Main Warehouse</td>
                                <td>50</td>
                                <td>48</td>
                                <td class="text-danger">-2</td>
                                <td>Damaged goods</td>
                                <td>2024-01-15</td>
                            </tr>
                            <tr>
                                <td>ADJ002</td>
                                <td>Samsung Galaxy</td>
                                <td>Branch Store</td>
                                <td>25</td>
                                <td>27</td>
                                <td class="text-success">+2</td>
                                <td>Found in storage</td>
                                <td>2024-01-14</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection