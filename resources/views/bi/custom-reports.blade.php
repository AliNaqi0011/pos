@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>📊 Custom Reports</h4>
                <button class="btn btn-primary btn-sm float-right">Create New Report</button>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="typcn typcn-chart-bar" style="font-size: 3rem; color: #007bff;"></i>
                                <h5 class="mt-2">Sales Report</h5>
                                <p class="text-muted">Detailed sales analysis</p>
                                <form action="{{ route('bi.generate-report') }}" method="POST" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="type" value="sales">
                                    <input type="hidden" name="date_range" value="last_30_days">
                                    <input type="hidden" name="format" value="pdf">
                                    <button type="submit" class="btn btn-sm btn-primary">Generate</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="typcn typcn-user" style="font-size: 3rem; color: #28a745;"></i>
                                <h5 class="mt-2">Customer Report</h5>
                                <p class="text-muted">Customer behavior analysis</p>
                                <form action="{{ route('bi.generate-report') }}" method="POST" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="type" value="customer">
                                    <input type="hidden" name="date_range" value="last_30_days">
                                    <input type="hidden" name="format" value="pdf">
                                    <button type="submit" class="btn btn-sm btn-success">Generate</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="typcn typcn-shopping-cart" style="font-size: 3rem; color: #ffc107;"></i>
                                <h5 class="mt-2">Inventory Report</h5>
                                <p class="text-muted">Stock and inventory analysis</p>
                                <form action="{{ route('bi.generate-report') }}" method="POST" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="type" value="inventory">
                                    <input type="hidden" name="date_range" value="last_30_days">
                                    <input type="hidden" name="format" value="pdf">
                                    <button type="submit" class="btn btn-sm btn-warning">Generate</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <h5>Recent Reports</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Report Name</th>
                                <th>Type</th>
                                <th>Created By</th>
                                <th>Date Created</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Monthly Sales Summary</td>
                                <td><span class="badge badge-primary">Sales</span></td>
                                <td>Admin User</td>
                                <td>2024-01-15</td>
                                <td><span class="badge badge-success">Ready</span></td>
                                <td>
                                    <button class="btn btn-sm btn-info">View</button>
                                    <button class="btn btn-sm btn-secondary">Download</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Customer Segmentation Analysis</td>
                                <td><span class="badge badge-success">Customer</span></td>
                                <td>Admin User</td>
                                <td>2024-01-14</td>
                                <td><span class="badge badge-warning">Processing</span></td>
                                <td>
                                    <button class="btn btn-sm btn-info" disabled>View</button>
                                    <button class="btn btn-sm btn-secondary" disabled>Download</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Inventory Valuation Report</td>
                                <td><span class="badge badge-warning">Inventory</span></td>
                                <td>Admin User</td>
                                <td>2024-01-13</td>
                                <td><span class="badge badge-success">Ready</span></td>
                                <td>
                                    <button class="btn btn-sm btn-info">View</button>
                                    <button class="btn btn-sm btn-secondary">Download</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Report Builder</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('bi.generate-report') }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label>Report Type</label>
                                        <select class="form-control" name="type" required>
                                            <option value="sales">Sales Analysis</option>
                                            <option value="customer">Customer Analysis</option>
                                            <option value="inventory">Inventory Analysis</option>
                                            <option value="financial">Financial Analysis</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Date Range</label>
                                        <select class="form-control" name="date_range" required>
                                            <option value="last_7_days">Last 7 Days</option>
                                            <option value="last_30_days">Last 30 Days</option>
                                            <option value="last_3_months">Last 3 Months</option>
                                            <option value="all_time">All Time</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Format</label>
                                        <select class="form-control" name="format" required>
                                            <option value="pdf">PDF</option>
                                            <option value="excel">Excel</option>
                                            <option value="csv">CSV</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Generate Report</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Scheduled Reports</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6>Weekly Sales Report</h6>
                                            <small class="text-muted">Every Monday at 9:00 AM</small>
                                        </div>
                                        <span class="badge badge-success">Active</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6>Monthly Inventory Report</h6>
                                            <small class="text-muted">1st of every month</small>
                                        </div>
                                        <span class="badge badge-success">Active</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6>Customer Analysis Report</h6>
                                            <small class="text-muted">Every 15th of month</small>
                                        </div>
                                        <span class="badge badge-secondary">Paused</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection