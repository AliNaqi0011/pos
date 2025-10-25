@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>💰 Inventory Valuation</h4>
                <small class="text-muted">As of {{ date('F d, Y') }}</small>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5>Total Inventory Value</h5>
                                <h3>$125,450.00</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5>Fast Moving</h5>
                                <h3>$85,200.00</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5>Slow Moving</h5>
                                <h3>$25,150.00</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h5>Dead Stock</h5>
                                <h3>$15,100.00</h3>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Unit Cost</th>
                                <th>Total Value</th>
                                <th>Category</th>
                                <th>Last Movement</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>iPhone 15 Pro</td>
                                <td>25</td>
                                <td>$999.00</td>
                                <td>$24,975.00</td>
                                <td><span class="badge badge-success">Fast Moving</span></td>
                                <td>2024-01-15</td>
                            </tr>
                            <tr>
                                <td>Samsung Galaxy S24</td>
                                <td>15</td>
                                <td>$899.00</td>
                                <td>$13,485.00</td>
                                <td><span class="badge badge-success">Fast Moving</span></td>
                                <td>2024-01-14</td>
                            </tr>
                            <tr>
                                <td>Old iPhone Model</td>
                                <td>10</td>
                                <td>$299.00</td>
                                <td>$2,990.00</td>
                                <td><span class="badge badge-danger">Dead Stock</span></td>
                                <td>2023-12-01</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection