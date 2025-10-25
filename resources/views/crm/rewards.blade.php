@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>🎁 Rewards Catalog</h4>
                <button class="btn btn-primary btn-sm float-right">Add Reward</button>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="typcn typcn-gift" style="font-size: 3rem; color: #007bff;"></i>
                                <h5 class="mt-2">10% Discount</h5>
                                <p class="text-muted">Valid on next purchase</p>
                                <h4 class="text-primary">500 Points</h4>
                                <button class="btn btn-sm btn-outline-primary">Edit</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="typcn typcn-shopping-cart" style="font-size: 3rem; color: #28a745;"></i>
                                <h5 class="mt-2">Free Shipping</h5>
                                <p class="text-muted">On orders over $50</p>
                                <h4 class="text-success">300 Points</h4>
                                <button class="btn btn-sm btn-outline-success">Edit</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="typcn typcn-star" style="font-size: 3rem; color: #ffc107;"></i>
                                <h5 class="mt-2">Exclusive Product</h5>
                                <p class="text-muted">Limited edition item</p>
                                <h4 class="text-warning">2000 Points</h4>
                                <button class="btn btn-sm btn-outline-warning">Edit</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <h5 class="mt-4">Recent Redemptions</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Reward</th>
                                <th>Points Used</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>John Smith</td>
                                <td>10% Discount</td>
                                <td>500</td>
                                <td>2024-01-15</td>
                                <td><span class="badge badge-success">Redeemed</span></td>
                            </tr>
                            <tr>
                                <td>Sarah Johnson</td>
                                <td>Free Shipping</td>
                                <td>300</td>
                                <td>2024-01-14</td>
                                <td><span class="badge badge-info">Used</span></td>
                            </tr>
                            <tr>
                                <td>Mike Wilson</td>
                                <td>Exclusive Product</td>
                                <td>2000</td>
                                <td>2024-01-13</td>
                                <td><span class="badge badge-warning">Pending</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection