@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>⚠️ Low Stock Alerts</h4>
                <button class="btn btn-warning btn-sm float-right">Set Alert Levels</button>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <strong>{{ $lowStockProducts->count() }} products</strong> are running low on stock!
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Current Stock</th>
                                <th>Minimum Level</th>
                                <th>Warehouse</th>
                                <th>Last Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowStockProducts as $product)
                            <tr class="{{ $product['current_stock'] == 0 ? 'table-danger' : 'table-warning' }}">
                                <td>{{ $product['name'] }}</td>
                                <td>{{ $product['current_stock'] }}</td>
                                <td>{{ $product['minimum_level'] }}</td>
                                <td>{{ $product['warehouse'] }}</td>
                                <td>{{ $product['last_updated'] }}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary">Reorder</button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-success">
                                    <i class="typcn typcn-thumbs-up"></i> All products are well stocked!
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection