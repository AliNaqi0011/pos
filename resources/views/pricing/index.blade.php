@extends('layouts.main')

@section('content')
<style>
.pricing-card {
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    border: none;
    overflow: hidden;
}
.pricing-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}
.pricing-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    text-align: center;
}
.price {
    font-size: 3rem;
    font-weight: bold;
    margin: 1rem 0;
}
.currency {
    font-size: 1.2rem;
    opacity: 0.8;
}
.period {
    font-size: 1rem;
    opacity: 0.9;
}
.popular-badge {
    position: absolute;
    top: -10px;
    right: 20px;
    background: #ff6b6b;
    color: white;
    padding: 5px 20px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: bold;
}
.feature-list {
    padding: 2rem;
}
.feature-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f0f0f0;
}
.feature-item:last-child {
    border-bottom: none;
}
.btn-pricing {
    width: 100%;
    padding: 1rem;
    font-size: 1.1rem;
    font-weight: bold;
    border-radius: 50px;
    margin: 1rem;
    transition: all 0.3s ease;
}
.btn-pricing:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}
</style>

<div class="main-panel">
    <div class="content-wrapper">
        <!-- Header Section -->
        <div class="text-center mb-5">
            <h1 class="display-4 font-weight-bold text-primary">Choose Your Plan</h1>
            <p class="lead text-muted">Select the perfect plan for your business needs</p>
        </div>

        <!-- Pricing Cards -->
        <div class="row justify-content-center">
            <!-- Monthly Plan -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card pricing-card h-100">
                    <div class="pricing-header">
                        <h3 class="mb-0">Monthly</h3>
                        <div class="price">
                            <span class="currency">PKR</span> 1,200
                        </div>
                        <p class="period">per month</p>
                    </div>
                    <div class="feature-list">
                        <div class="feature-item">
                            <i class="typcn typcn-tick text-success"></i>
                            Complete POS System
                        </div>
                        <div class="feature-item">
                            <i class="typcn typcn-tick text-success"></i>
                            Inventory Management
                        </div>
                        <div class="feature-item">
                            <i class="typcn typcn-tick text-success"></i>
                            Sales & Purchase Tracking
                        </div>
                        <div class="feature-item">
                            <i class="typcn typcn-tick text-success"></i>
                            Customer Management
                        </div>
                        <div class="feature-item">
                            <i class="typcn typcn-tick text-success"></i>
                            Basic Reports
                        </div>
                        <div class="feature-item">
                            <i class="typcn typcn-tick text-success"></i>
                            Email Support
                        </div>
                    </div>
                    <div class="text-center pb-4">
                        <button class="btn btn-outline-primary btn-pricing">
                            Get Started
                        </button>
                    </div>
                </div>
            </div>

            <!-- 6 Month Plan -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card pricing-card h-100 position-relative">
                    <div class="popular-badge">Most Popular</div>
                    <div class="pricing-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <h3 class="mb-0">6 Months</h3>
                        <div class="price">
                            <span class="currency">PKR</span> 5,599
                        </div>
                        <p class="period">6 months</p>
                        <small class="badge badge-light text-dark">Save PKR 1,601</small>
                    </div>
                    <div class="feature-list">
                        <div class="feature-item">
                            <i class="typcn typcn-tick text-success"></i>
                            Everything in Monthly
                        </div>
                        <div class="feature-item">
                            <i class="typcn typcn-tick text-success"></i>
                            Advanced Reports & Analytics
                        </div>
                        <div class="feature-item">
                            <i class="typcn typcn-tick text-success"></i>
                            Barcode Generation
                        </div>
                        <div class="feature-item">
                            <i class="typcn typcn-tick text-success"></i>
                            Multi-user Access
                        </div>
                        <div class="feature-item">
                            <i class="typcn typcn-tick text-success"></i>
                            Priority Support
                        </div>
                        <div class="feature-item">
                            <i class="typcn typcn-tick text-success"></i>
                            Data Backup
                        </div>
                    </div>
                    <div class="text-center pb-4">
                        <button class="btn btn-primary btn-pricing">
                            Choose Plan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Yearly Plan -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card pricing-card h-100">
                    <div class="pricing-header" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <h3 class="mb-0">Yearly</h3>
                        <div class="price">
                            <span class="currency">PKR</span> 10,000
                        </div>
                        <p class="period">per year</p>
                        <small class="badge badge-light text-dark">Save PKR 4,400</small>
                    </div>
                    <div class="feature-list">
                        <div class="feature-item">
                            <i class="typcn typcn-tick text-success"></i>
                            Everything in 6 Months
                        </div>
                        <div class="feature-item">
                            <i class="typcn typcn-tick text-success"></i>
                            Custom Integrations
                        </div>
                        <div class="feature-item">
                            <i class="typcn typcn-tick text-success"></i>
                            API Access
                        </div>
                        <div class="feature-item">
                            <i class="typcn typcn-tick text-success"></i>
                            White Label Options
                        </div>
                        <div class="feature-item">
                            <i class="typcn typcn-tick text-success"></i>
                            24/7 Phone Support
                        </div>
                        <div class="feature-item">
                            <i class="typcn typcn-tick text-success"></i>
                            Free Updates
                        </div>
                    </div>
                    <div class="text-center pb-4">
                        <button class="btn btn-success btn-pricing">
                            Best Value
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Comparison -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Feature Comparison</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Features</th>
                                        <th class="text-center">Monthly</th>
                                        <th class="text-center">6 Months</th>
                                        <th class="text-center">Yearly</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>POS System</td>
                                        <td class="text-center"><i class="typcn typcn-tick text-success"></i></td>
                                        <td class="text-center"><i class="typcn typcn-tick text-success"></i></td>
                                        <td class="text-center"><i class="typcn typcn-tick text-success"></i></td>
                                    </tr>
                                    <tr>
                                        <td>Inventory Management</td>
                                        <td class="text-center"><i class="typcn typcn-tick text-success"></i></td>
                                        <td class="text-center"><i class="typcn typcn-tick text-success"></i></td>
                                        <td class="text-center"><i class="typcn typcn-tick text-success"></i></td>
                                    </tr>
                                    <tr>
                                        <td>Advanced Reports</td>
                                        <td class="text-center"><i class="typcn typcn-times text-danger"></i></td>
                                        <td class="text-center"><i class="typcn typcn-tick text-success"></i></td>
                                        <td class="text-center"><i class="typcn typcn-tick text-success"></i></td>
                                    </tr>
                                    <tr>
                                        <td>Multi-user Access</td>
                                        <td class="text-center"><i class="typcn typcn-times text-danger"></i></td>
                                        <td class="text-center"><i class="typcn typcn-tick text-success"></i></td>
                                        <td class="text-center"><i class="typcn typcn-tick text-success"></i></td>
                                    </tr>
                                    <tr>
                                        <td>API Access</td>
                                        <td class="text-center"><i class="typcn typcn-times text-danger"></i></td>
                                        <td class="text-center"><i class="typcn typcn-times text-danger"></i></td>
                                        <td class="text-center"><i class="typcn typcn-tick text-success"></i></td>
                                    </tr>
                                    <tr>
                                        <td>24/7 Support</td>
                                        <td class="text-center"><i class="typcn typcn-times text-danger"></i></td>
                                        <td class="text-center"><i class="typcn typcn-times text-danger"></i></td>
                                        <td class="text-center"><i class="typcn typcn-tick text-success"></i></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection