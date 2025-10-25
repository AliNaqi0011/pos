@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>📦 Stock Transfers</h4>
                <button class="btn btn-primary btn-sm float-right">New Transfer</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Transfer ID</th>
                                <th>From Warehouse</th>
                                <th>To Warehouse</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>ST001</td>
                                <td>Main Warehouse</td>
                                <td>Branch Store</td>
                                <td>iPhone 15</td>
                                <td>10</td>
                                <td>2024-01-15</td>
                                <td><span class="badge badge-success">Completed</span></td>
                                <td>
                                    <button class="btn btn-sm btn-info">View</button>
                                </td>
                            </tr>
                            <tr>
                                <td>ST002</td>
                                <td>Branch Store</td>
                                <td>Main Warehouse</td>
                                <td>Samsung Galaxy</td>
                                <td>5</td>
                                <td>2024-01-14</td>
                                <td><span class="badge badge-warning">Pending</span></td>
                                <td>
                                    <button class="btn btn-sm btn-success">Approve</button>
                                    <button class="btn btn-sm btn-danger">Cancel</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection