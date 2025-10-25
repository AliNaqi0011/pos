<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InfyPOS - Point of Sale</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
            overflow-x: hidden;
        }

        .pos-container {
            display: flex;
            height: 100vh;
            background: #fff;
        }

        /* Left Panel - Products */
        .products-panel {
            flex: 1;
            background: #fff;
            border-right: 1px solid #e9ecef;
            display: flex;
            flex-direction: column;
        }

        .pos-header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            border-radius: 0 0 20px 20px;
        }

        .pos-title {
            font-size: 28px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .pos-time {
            font-size: 14px;
            opacity: 0.9;
        }

        /* Filters Section */
        .filters-section {
            padding: 25px;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-bottom: 2px solid #e2e8f0;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
        }

        .filter-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        .btn {
            padding: 8px 16px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 12px;
            font-weight: 600;
        }
        
        .btn-outline-primary {
            color: #667eea;
            border-color: #667eea;
        }
        
        .btn-outline-primary:hover {
            background: #667eea;
            color: white;
        }

        .filter-group label {
            font-size: 12px;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            margin-bottom: 5px;
            display: block;
        }

        .filter-select {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            font-size: 14px;
            background: white;
            transition: all 0.3s ease;
        }

        .filter-select:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15);
            transform: translateY(-1px);
        }

        .search-container {
            position: relative;
            flex: 2;
        }

        .search-input {
            width: 100%;
            padding: 12px 45px 12px 15px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15);
            transform: translateY(-1px);
        }

        .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        /* Products Grid */
        .products-grid {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }

        .products-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }

        .product-card {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .product-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(79, 70, 229, 0.15);
            border-color: #4f46e5;
        }

        .product-image {
            width: 100%;
            height: 120px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-image .no-image {
            color: #dee2e6;
            font-size: 48px;
        }

        .stock-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .stock-in { background: #d4edda; color: #155724; }
        .stock-low { background: #fff3cd; color: #856404; }
        .stock-out { background: #f8d7da; color: #721c24; }

        .product-info {
            padding: 15px;
        }

        .product-name {
            font-size: 14px;
            font-weight: 600;
            color: #212529;
            margin-bottom: 5px;
            line-height: 1.3;
        }

        .product-code {
            font-size: 11px;
            color: #6c757d;
            margin-bottom: 8px;
        }

        .product-price {
            font-size: 16px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 5px;
        }

        .product-stock {
            font-size: 11px;
            color: #6c757d;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Right Panel - Cart */
        .cart-panel {
            width: 400px;
            background: white;
            display: flex;
            flex-direction: column;
            box-shadow: -2px 0 10px rgba(0,0,0,0.1);
        }

        .cart-header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 25px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .cart-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .cart-subtitle {
            font-size: 12px;
            opacity: 0.9;
        }

        /* Customer Selection */
        .customer-section {
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
        }

        .customer-label {
            font-size: 12px;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .customer-select {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            font-size: 14px;
            background: white;
        }

        /* Cart Items */
        .cart-items {
            flex: 1;
            overflow-y: auto;
            padding: 0;
            max-height: 400px;
            min-height: 300px;
        }

        .cart-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 200px;
            color: #6c757d;
        }

        .cart-empty i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        .cart-item {
            padding: 12px 15px;
            border-bottom: 1px solid #f1f3f4;
            display: flex;
            align-items: center;
            gap: 10px;
            min-height: 70px;
            transition: all 0.3s ease;
        }
        
        .cart-item:hover {
            background: #f8fafc;
            transform: translateX(5px);
        }

        .cart-item-image {
            width: 45px;
            height: 45px;
            border-radius: 8px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
        }

        .cart-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .cart-item-info {
            flex: 1;
        }

        .cart-item-name {
            font-size: 12px;
            font-weight: 600;
            color: #212529;
            margin-bottom: 2px;
            line-height: 1.2;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 120px;
        }

        .cart-item-price {
            font-size: 11px;
            color: #4f46e5;
            font-weight: 600;
        }

        .cart-item-controls {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        .qty-btn {
            width: 24px;
            height: 24px;
            border: 1px solid #dee2e6;
            background: white;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 12px;
        }

        .qty-btn:hover {
            background: #4f46e5;
            color: white;
            border-color: #4f46e5;
            transform: scale(1.1);
        }

        .qty-input {
            width: 40px;
            text-align: center;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 2px;
            font-size: 11px;
            height: 24px;
        }

        .remove-btn {
            color: #dc3545;
            cursor: pointer;
            padding: 2px;
            border-radius: 4px;
            transition: all 0.2s ease;
            font-size: 12px;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .remove-btn:hover {
            background: #f8d7da;
        }

        /* Cart Summary */
        .cart-summary {
            padding: 20px;
            border-top: 1px solid #e9ecef;
            background: #f8f9fa;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .summary-row.total {
            font-size: 16px;
            font-weight: 700;
            color: #212529;
            padding-top: 8px;
            border-top: 1px solid #dee2e6;
            margin-top: 8px;
        }

        /* Discount Section */
        .discount-section {
            padding: 15px 20px;
            border-top: 1px solid #e9ecef;
        }

        .discount-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            color: #667eea;
        }

        .discount-form {
            margin-top: 15px;
            display: none;
        }

        .discount-form.active {
            display: block;
        }

        .discount-inputs {
            display: flex;
            gap: 10px;
        }

        .discount-type {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            font-size: 12px;
        }

        .discount-value {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            font-size: 12px;
        }

        /* Payment Section */
        .payment-section {
            padding: 20px;
            border-top: 1px solid #e9ecef;
        }

        .payment-methods {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 15px;
        }

        .payment-method {
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 12px;
            font-weight: 600;
        }

        .payment-method.active {
            border-color: #4f46e5;
            background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
            color: #4f46e5;
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
        }

        .payment-method i {
            display: block;
            font-size: 20px;
            margin-bottom: 5px;
        }

        .checkout-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .checkout-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.4);
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
        }

        .checkout-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .pos-container {
                flex-direction: column;
            }
            
            .cart-panel {
                width: 100%;
                height: 50vh;
            }
            
            .products-container {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: 15px;
            }
        }

        /* Loading States */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Receipt Modal Styling */
        .receipt-modal {
            font-family: 'Poppins', sans-serif !important;
        }
        
        .receipt-title {
            font-size: 20px !important;
            color: #333 !important;
        }
    </style>
</head>
<body>
    <div class="pos-container">
        <!-- Products Panel -->
        <div class="products-panel">
            <!-- Header -->
            <div class="pos-header">
                <div class="pos-title">
                    <i class="fas fa-store"></i>
                    Smart POS System
                </div>
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-light btn-sm" id="dashboard-btn" onclick="goToDashboard()" title="Go to Dashboard">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </button>
                    <button class="btn btn-light btn-sm" id="fullscreen-btn" onclick="toggleFullscreen()" title="Toggle Fullscreen">
                        <i class="fas fa-expand"></i> Fullscreen
                    </button>
                    <div class="pos-time" id="current-time"></div>
                    <div class="badge badge-light px-3 py-2">
                        <i class="fas fa-user"></i> {{ Auth::user()->name }}
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="filters-section">
                <div class="filter-row">
                    <div class="filter-group">
                        <label>Category</label>
                        <select class="filter-select" id="category-filter">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Brand</label>
                        <select class="filter-select" id="brand-filter">
                            <option value="">All Brands</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="search-container">
                        <label>Search Products / Scan Barcode</label>
                        <input type="text" class="search-input" id="product-search" placeholder="Search by name, code or scan barcode...">
                        <i class="fas fa-search search-icon"></i>
                    </div>
                    <div class="filter-group">
                        <label>Barcode Scanner</label>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="barcode-scanner-btn">
                            <i class="fas fa-barcode"></i> Scan Barcode
                        </button>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="products-grid">
                <div class="products-container" id="products-container">
                    @foreach($products as $product)
                        @php
                            $stockStatus = 'stock-in';
                            $stockText = 'In Stock';
                            $quantity = $product->quantity ?? 0;
                            $alertLevel = $product->stock_alert_level ?? 10;
                            if($quantity <= 0) {
                                $stockStatus = 'stock-out';
                                $stockText = 'Out of Stock';
                            } elseif($quantity <= $alertLevel) {
                                $stockStatus = 'stock-low';
                                $stockText = 'Low Stock';
                            }
                        @endphp
                        <div class="product-card" 
                             data-id="{{ $product->id }}"
                             data-name="{{ $product->name }}"
                             data-price="{{ $product->sale_price ?? $product->price }}"
                             data-category="{{ $product->category_id }}"
                             data-brand="{{ $product->brand_id }}"
                             data-stock="{{ $product->quantity ?? 0 }}">
                            <div class="product-image">
                                @if($product->image && file_exists(public_path('storage/' . $product->image)))
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                                @else
                                    <i class="fas fa-box no-image"></i>
                                @endif
                                <span class="stock-badge {{ $stockStatus }}">{{ $stockText }}</span>
                            </div>
                            <div class="product-info">
                                <div class="product-name">{{ $product->name }}</div>
                                <div class="product-code">{{ $product->code ?? 'N/A' }}</div>
                                <div class="product-price">${{ number_format($product->sale_price ?? $product->price, 2) }}</div>
                                <div class="product-stock">
                                    <i class="fas fa-cube"></i>
                                    {{ $product->quantity ?? 0 }} {{ $product->unit ?? 'pcs' }} available
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Cart Panel -->
        <div class="cart-panel">
            <!-- Cart Header -->
            <div class="cart-header">
                <div class="cart-title">🛒 Shopping Cart</div>
                <div class="cart-subtitle">Build your perfect order</div>
            </div>

            <!-- Customer Selection -->
            <div class="customer-section">
                <div class="customer-label">Select Customer</div>
                <select class="customer-select" id="customer-select">
                    <option value="">Walk-in Customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Cart Items -->
            <div class="cart-items" id="cart-items">
                <div class="cart-empty">
                    <i class="fas fa-shopping-cart"></i>
                    <div>No items in cart</div>
                    <small>Add products to get started</small>
                </div>
            </div>

            <!-- Discount Section -->
            <div class="discount-section">
                <div class="discount-toggle" onclick="toggleDiscount()">
                    <span><i class="fas fa-percent"></i> Apply Discount</span>
                    <i class="fas fa-chevron-down" id="discount-arrow"></i>
                </div>
                <div class="discount-form" id="discount-form">
                    <div class="discount-inputs">
                        <select class="discount-type" id="discount-type">
                            <option value="fixed">Fixed ($)</option>
                            <option value="percentage">Percentage (%)</option>
                        </select>
                        <input type="number" class="discount-value" id="discount-value" placeholder="0" min="0" step="0.01">
                    </div>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="cart-summary">
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span id="subtotal">$0.00</span>
                </div>
                <div class="summary-row">
                    <span>Discount:</span>
                    <span id="discount-amount">$0.00</span>
                </div>
                <div class="summary-row">
                    <span>Tax (10%):</span>
                    <span id="tax-amount">$0.00</span>
                </div>
                <div class="summary-row total">
                    <span>Total:</span>
                    <span id="total-amount">$0.00</span>
                </div>
            </div>

            <!-- Payment Section -->
            <div class="payment-section">
                <div class="payment-methods">
                    <div class="payment-method active" data-method="cash">
                        <i class="fas fa-money-bill-wave"></i>
                        Cash
                    </div>
                    <div class="payment-method" data-method="card">
                        <i class="fas fa-credit-card"></i>
                        Card
                    </div>
                    <div class="payment-method" data-method="mobile">
                        <i class="fas fa-mobile-alt"></i>
                        Mobile Pay
                    </div>
                    <div class="payment-method" data-method="bank">
                        <i class="fas fa-university"></i>
                        Bank Transfer
                    </div>
                </div>
                <button class="checkout-btn" id="checkout-btn" disabled>
                    <i class="fas fa-credit-card"></i>
                    Process Payment
                </button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Global variables
        let cart = [];
        let selectedPaymentMethod = 'cash';
        let discountType = 'fixed';
        let discountValue = 0;

        // Initialize
        $(document).ready(function() {
            updateTime();
            setInterval(updateTime, 1000);
            setupEventListeners();
            updateCartDisplay();
        });

        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', { 
                hour12: true, 
                hour: '2-digit', 
                minute: '2-digit' 
            });
            const dateString = now.toLocaleDateString('en-US', { 
                weekday: 'short', 
                month: 'short', 
                day: 'numeric' 
            });
            $('#current-time').text(`${dateString} ${timeString}`);
        }

        function setupEventListeners() {
            // Product card click
            $(document).on('click', '.product-card', function() {
                const productId = $(this).data('id');
                const productName = $(this).data('name');
                const productPrice = parseFloat($(this).data('price'));
                const productStock = parseInt($(this).data('stock'));

                if (productStock <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Out of Stock',
                        text: 'This product is currently out of stock.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    return;
                }

                addToCart(productId, productName, productPrice);
            });

            // Search functionality with barcode support
            $('#product-search').on('input', function() {
                const searchValue = $(this).val();
                if (searchValue.length >= 8) { // Assume barcode if 8+ characters
                    searchByBarcode(searchValue);
                } else {
                    filterProducts();
                }
            });
            
            // Barcode scanner button
            $('#barcode-scanner-btn').on('click', function() {
                Swal.fire({
                    title: 'Scan Barcode',
                    input: 'text',
                    inputPlaceholder: 'Enter or scan barcode...',
                    showCancelButton: true,
                    confirmButtonText: 'Search',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Please enter a barcode!'
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        searchByBarcode(result.value);
                    }
                });
            });

            // Filter functionality
            $('#category-filter, #brand-filter').on('change', function() {
                filterProducts();
            });

            // Payment method selection
            $('.payment-method').on('click', function() {
                $('.payment-method').removeClass('active');
                $(this).addClass('active');
                selectedPaymentMethod = $(this).data('method');
            });

            // Discount inputs
            $('#discount-type').on('change', function() {
                discountType = $(this).val();
                calculateTotals();
            });

            $('#discount-value').on('input', function() {
                discountValue = parseFloat($(this).val()) || 0;
                calculateTotals();
            });

            // Checkout button
            $('#checkout-btn').on('click', function() {
                if (cart.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Empty Cart',
                        text: 'Please add items to cart before checkout.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    return;
                }
                processCheckout();
            });
        }

        function addToCart(productId, productName, productPrice) {
            const existingItem = cart.find(item => item.id === productId);
            
            if (existingItem) {
                existingItem.quantity += 1;
                existingItem.total = existingItem.quantity * existingItem.price;
            } else {
                cart.push({
                    id: productId,
                    name: productName,
                    price: productPrice,
                    quantity: 1,
                    total: productPrice
                });
            }

            updateCartDisplay();
            calculateTotals();

            // Show success animation
            Swal.fire({
                icon: 'success',
                title: 'Added to Cart',
                text: `${productName} added successfully!`,
                timer: 1500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }

        function updateCartDisplay() {
            const cartContainer = $('#cart-items');
            
            if (cart.length === 0) {
                cartContainer.html(`
                    <div class="cart-empty">
                        <i class="fas fa-shopping-cart"></i>
                        <div>No items in cart</div>
                        <small>Add products to get started</small>
                    </div>
                `);
                $('#checkout-btn').prop('disabled', true);
                return;
            }

            let cartHtml = '';
            cart.forEach((item, index) => {
                cartHtml += `
                    <div class="cart-item">
                        <div class="cart-item-image">
                            <i class="fas fa-box" style="font-size: 16px; color: #6b7280;"></i>
                        </div>
                        <div class="cart-item-info">
                            <div class="cart-item-name" title="${item.name}">${item.name}</div>
                            <div class="cart-item-price">$${item.price.toFixed(2)}</div>
                        </div>
                        <div class="cart-item-controls">
                            <button class="qty-btn" onclick="updateQuantity(${index}, -1)">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="qty-input" value="${item.quantity}" 
                                   onchange="setQuantity(${index}, this.value)" min="1">
                            <button class="qty-btn" onclick="updateQuantity(${index}, 1)">
                                <i class="fas fa-plus"></i>
                            </button>
                            <button class="remove-btn" onclick="removeFromCart(${index})" title="Remove item">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                `;
            });

            cartContainer.html(cartHtml);
            $('#checkout-btn').prop('disabled', false);
        }

        function updateQuantity(index, change) {
            if (cart[index]) {
                cart[index].quantity += change;
                if (cart[index].quantity <= 0) {
                    cart.splice(index, 1);
                } else {
                    cart[index].total = cart[index].quantity * cart[index].price;
                }
                updateCartDisplay();
                calculateTotals();
            }
        }

        function setQuantity(index, quantity) {
            quantity = parseInt(quantity);
            if (quantity <= 0) {
                removeFromCart(index);
            } else {
                cart[index].quantity = quantity;
                cart[index].total = cart[index].quantity * cart[index].price;
                calculateTotals();
            }
        }

        function removeFromCart(index) {
            cart.splice(index, 1);
            updateCartDisplay();
            calculateTotals();
        }

        function calculateTotals() {
            const subtotal = cart.reduce((sum, item) => sum + item.total, 0);
            
            let discount = 0;
            if (discountValue > 0) {
                if (discountType === 'percentage') {
                    discount = (subtotal * discountValue) / 100;
                } else {
                    discount = discountValue;
                }
            }

            const taxableAmount = subtotal - discount;
            const tax = taxableAmount * 0.10; // 10% tax
            const total = taxableAmount + tax;

            $('#subtotal').text(`$${subtotal.toFixed(2)}`);
            $('#discount-amount').text(`-$${discount.toFixed(2)}`);
            $('#tax-amount').text(`$${tax.toFixed(2)}`);
            $('#total-amount').text(`$${total.toFixed(2)}`);
        }

        function toggleDiscount() {
            const form = $('#discount-form');
            const arrow = $('#discount-arrow');
            
            form.toggleClass('active');
            arrow.toggleClass('fa-chevron-down fa-chevron-up');
        }

        function searchByBarcode(barcode) {
            $.ajax({
                url: '{{ route("barcodes.scan") }}',
                method: 'GET',
                data: { barcode: barcode },
                success: function(response) {
                    if (response.success) {
                        const product = response.product;
                        
                        // Add to cart directly
                        addToCart(product.id, product.name, product.sale_price);
                        
                        // Clear search
                        $('#product-search').val('');
                        
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Product Found!',
                            text: `${product.name} added to cart`,
                            timer: 2000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Product Not Found',
                            text: response.message || 'No product found with this barcode',
                            timer: 3000,
                            showConfirmButton: false
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Search Error',
                        text: 'Unable to search for product. Please try again.',
                        timer: 3000,
                        showConfirmButton: false
                    });
                }
            });
        }

        function filterProducts() {
            const searchTerm = $('#product-search').val().toLowerCase();
            const categoryFilter = $('#category-filter').val();
            const brandFilter = $('#brand-filter').val();

            $('.product-card').each(function() {
                const productName = $(this).data('name').toLowerCase();
                const productCategory = $(this).data('category').toString();
                const productBrand = $(this).data('brand').toString();

                let show = true;

                // Search filter
                if (searchTerm && !productName.includes(searchTerm)) {
                    show = false;
                }

                // Category filter
                if (categoryFilter && productCategory !== categoryFilter) {
                    show = false;
                }

                // Brand filter
                if (brandFilter && productBrand !== brandFilter) {
                    show = false;
                }

                $(this).toggle(show);
            });
        }

        function showReceipt(receipt) {
            if (!receipt || !receipt.sale_id) {
                console.error('Invalid receipt data:', receipt);
                Swal.fire({
                    icon: 'success',
                    title: 'Sale Completed!',
                    text: 'Sale was successful but receipt data is unavailable.',
                    confirmButtonText: 'New Sale'
                }).then(() => {
                    resetPOS();
                });
                return;
            }
            
            const receiptHtml = `
                <div style="font-family: 'Courier New', monospace; max-width: 400px; margin: 0 auto; background: white; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
                    <!-- Header -->
                    <div style="text-align: center; border-bottom: 2px solid #333; padding-bottom: 15px; margin-bottom: 20px;">
                        <h2 style="margin: 0; font-size: 24px; font-weight: bold; color: #333;">🏪 SMART POS</h2>
                        <p style="margin: 5px 0 0 0; font-size: 12px; color: #666;">Point of Sale System</p>
                        <p style="margin: 5px 0 0 0; font-size: 11px; color: #888;">📍 123 Business Street, City</p>
                        <p style="margin: 2px 0 0 0; font-size: 11px; color: #888;">📞 +1 (555) 123-4567</p>
                    </div>
                    
                    <!-- Sale Info -->
                    <div style="margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <span style="font-weight: bold;">Receipt #:</span>
                            <span>#${receipt.sale_id}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <span style="font-weight: bold;">Date:</span>
                            <span>${new Date().toLocaleDateString()} ${new Date().toLocaleTimeString()}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <span style="font-weight: bold;">Customer:</span>
                            <span>${receipt.customer || 'Walk-in Customer'}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="font-weight: bold;">Cashier:</span>
                            <span>{{ Auth::user()->name }}</span>
                        </div>
                    </div>
                    
                    <!-- Items -->
                    <div style="border-top: 1px dashed #666; border-bottom: 1px dashed #666; padding: 15px 0;">
                        <h4 style="margin: 0 0 10px 0; font-size: 14px; text-align: center; text-transform: uppercase;">Items Purchased</h4>
                        ${(receipt.items || []).map(item => `
                            <div style="margin-bottom: 8px;">
                                <div style="display: flex; justify-content: space-between; font-weight: bold;">
                                    <span>${item.product_name || item.name || 'Product'}</span>
                                    <span>$${(item.total || (item.price || 0) * (item.quantity || 1)).toFixed(2)}</span>
                                </div>
                                <div style="font-size: 11px; color: #666; margin-left: 10px;">
                                    ${item.quantity || 1} × $${(item.price || 0).toFixed(2)} each
                                </div>
                            </div>
                        `).join('')}
                    </div>
                    
                    <!-- Totals -->
                    <div style="margin-top: 15px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <span>Subtotal:</span>
                            <span>$${(receipt.subtotal || 0).toFixed(2)}</span>
                        </div>
                        ${(receipt.discount || 0) > 0 ? `
                            <div style="display: flex; justify-content: space-between; margin-bottom: 5px; color: #d63384;">
                                <span>Discount:</span>
                                <span>-$${(receipt.discount || 0).toFixed(2)}</span>
                            </div>
                        ` : ''}
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <span>Tax (10%):</span>
                            <span>$${(receipt.tax || 0).toFixed(2)}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 18px; font-weight: bold; border-top: 2px solid #333; padding-top: 8px; margin-top: 8px;">
                            <span>TOTAL:</span>
                            <span>$${(receipt.total || 0).toFixed(2)}</span>
                        </div>
                    </div>
                    
                    <!-- Payment Info -->
                    <div style="margin-top: 20px; text-align: center; padding-top: 15px; border-top: 1px dashed #666;">
                        <p style="margin: 0; font-weight: bold;">💳 Payment Method: ${(receipt.payment_method || 'cash').toUpperCase()}</p>
                        <p style="margin: 10px 0 0 0; font-size: 12px; color: #666;">Amount Paid: $${(receipt.total || 0).toFixed(2)}</p>
                        <p style="margin: 5px 0 0 0; font-size: 12px; color: #666;">Change: $0.00</p>
                    </div>
                    
                    <!-- Footer -->
                    <div style="margin-top: 20px; text-align: center; border-top: 1px dashed #666; padding-top: 15px;">
                        <p style="margin: 0; font-size: 14px; font-weight: bold;">✨ Thank You for Your Business! ✨</p>
                        <p style="margin: 5px 0 0 0; font-size: 11px; color: #666;">Please keep this receipt for your records</p>
                        <p style="margin: 5px 0 0 0; font-size: 10px; color: #888;">Visit us again soon!</p>
                        <div style="margin-top: 15px; font-size: 10px; color: #aaa;">
                            <p style="margin: 0;">Powered by Smart POS System</p>
                            <p style="margin: 2px 0 0 0;">www.smartpos.com</p>
                        </div>
                    </div>
                </div>
            `;
            
            Swal.fire({
                title: '<i class="fas fa-receipt" style="color: #10b981;"></i> Sale Completed Successfully!',
                html: receiptHtml,
                width: 500,
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-print"></i> Print Receipt',
                cancelButtonText: '<i class="fas fa-plus-circle"></i> New Sale',
                reverseButtons: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#10b981',
                customClass: {
                    popup: 'receipt-modal',
                    title: 'receipt-title'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    printReceipt(receiptHtml);
                }
                // Reset cart for new sale
                resetPOS();
            });
        }
        
        function printReceipt(receiptHtml) {
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                    <head><title>Receipt</title></head>
                    <body onload="window.print(); window.close();">
                        ${receiptHtml}
                    </body>
                </html>
            `);
        }
        
        function resetPOS() {
            cart = [];
            updateCartDisplay();
            calculateTotals();
            $('#customer-select').val('');
            $('#discount-value').val('');
            discountValue = 0;
            $('#discount-form').removeClass('active');
            $('#discount-arrow').removeClass('fa-chevron-up').addClass('fa-chevron-down');
        }

        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().then(() => {
                    $('#fullscreen-btn').html('<i class="fas fa-compress"></i> Exit Fullscreen');
                }).catch(err => {
                    console.log('Error attempting to enable fullscreen:', err);
                });
            } else {
                document.exitFullscreen().then(() => {
                    $('#fullscreen-btn').html('<i class="fas fa-expand"></i> Fullscreen');
                }).catch(err => {
                    console.log('Error attempting to exit fullscreen:', err);
                });
            }
        }

        function goToDashboard() {
            if (cart.length > 0) {
                Swal.fire({
                    title: 'Unsaved Items in Cart',
                    text: 'You have items in your cart. Are you sure you want to leave?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Leave',
                    cancelButtonText: 'Stay Here'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '/dashboard';
                    }
                });
            } else {
                window.location.href = '/dashboard';
            }
        }

        // Listen for fullscreen changes
        document.addEventListener('fullscreenchange', function() {
            if (document.fullscreenElement) {
                $('#fullscreen-btn').html('<i class="fas fa-compress"></i> Exit Fullscreen');
            } else {
                $('#fullscreen-btn').html('<i class="fas fa-expand"></i> Fullscreen');
            }
        });

        function processCheckout() {
            // STRICT: Only use existing customer ID - NEVER create new customers
            const customerSelectValue = $('#customer-select').val();
            const customerId = (customerSelectValue && 
                               customerSelectValue !== '' && 
                               customerSelectValue !== 'null' && 
                               customerSelectValue !== '0') ? customerSelectValue : null;
            
            // Log for debugging
            console.log('POS Checkout - Customer Selection:', {
                'raw_value': customerSelectValue,
                'processed_customer_id': customerId,
                'will_create_customer': false
            });
            const subtotal = cart.reduce((sum, item) => sum + item.total, 0);
            
            let discount = 0;
            if (discountValue > 0) {
                if (discountType === 'percentage') {
                    discount = (subtotal * discountValue) / 100;
                } else {
                    discount = discountValue;
                }
            }

            const taxableAmount = subtotal - discount;
            const tax = taxableAmount * 0.10;
            const total = taxableAmount + tax;

            // Transform cart items to match expected format
            const transformedItems = cart.map(item => ({
                product_id: item.id,
                price: item.price,
                quantity: item.quantity,
                total: item.total
            }));

            const orderData = {
                customer_id: customerId,
                items: transformedItems,
                total_items: cart.length,
                total_amount: total,
                discount_amount: discount,
                tax_amount: tax,
                payment_method: selectedPaymentMethod,
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            // Show loading
            $('#checkout-btn').html('<span class="spinner"></span> Processing...');
            $('#checkout-btn').prop('disabled', true);

            $.ajax({
                url: '/pos/checkout',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: orderData,
                success: function(response) {
                    console.log('Checkout response:', response);
                    if (response.success) {
                        // Generate receipt from current cart data
                        const receiptData = {
                            sale_id: response.sale_id || 'N/A',
                            date: new Date().toLocaleString(),
                            customer: $('#customer-select option:selected').text() || 'Walk-in Customer',
                            items: cart,
                            subtotal: cart.reduce((sum, item) => sum + item.total, 0),
                            discount: discountValue > 0 ? (discountType === 'percentage' ? 
                                (cart.reduce((sum, item) => sum + item.total, 0) * discountValue) / 100 : discountValue) : 0,
                            tax: 0,
                            total: total,
                            payment_method: selectedPaymentMethod
                        };
                        receiptData.tax = (receiptData.subtotal - receiptData.discount) * 0.10;
                        
                        showReceipt(receiptData);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Checkout Failed',
                            text: response.message || 'Please try again.'
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Please try again or contact support.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Checkout Failed',
                        text: errorMessage
                    });
                },
                complete: function() {
                    $('#checkout-btn').html('<i class="fas fa-credit-card"></i> Process Payment');
                    $('#checkout-btn').prop('disabled', false);
                }
            });
        }
    </script>
</body>
</html>