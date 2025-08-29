@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Reports Dashboard</h4>
                
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5>Sales Reports</h5>
                                        <p>Daily, Weekly, Monthly sales data</p>
                                    </div>
                                    <i class="typcn typcn-chart-bar" style="font-size: 48px;"></i>
                                </div>
                                <a href="{{ route('reports.sales') }}" class="btn btn-light btn-sm mt-2">View Report</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5>Products Report</h5>
                                        <p>Stock levels and product analysis</p>
                                    </div>
                                    <i class="typcn typcn-shopping-cart" style="font-size: 48px;"></i>
                                </div>
                                <a href="{{ route('reports.products') }}" class="btn btn-light btn-sm mt-2">View Report</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5>Customers Report</h5>
                                        <p>Customer analysis and statistics</p>
                                    </div>
                                    <i class="typcn typcn-group" style="font-size: 48px;"></i>
                                </div>
                                <a href="{{ route('reports.customers') }}" class="btn btn-light btn-sm mt-2">View Report</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection