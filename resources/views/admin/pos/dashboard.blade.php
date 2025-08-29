<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium POS System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="{{ asset('css/pos.css') }}" rel="stylesheet">
</head>
<body>

<div class="pos-container">
    <!-- Products Section -->
    <div class="product-section">
        <div class="pos-header">
            <div class="pos-title"> Point of Sale</div>
            <div class="pos-time" id="current-time"></div>
        </div>
        
        <div class="category-filter">
            <button class="category-btn active" data-category="all">All Products</button>
            @foreach ($categories as $category)
                <button class="category-btn" data-category="{{ $category->id }}">{{ $category->name }}</button>
            @endforeach
        </div>
        
        <div class="search-container">
            <i class="bi bi-search"></i>
            <input type="text" id="product-search" class="form-control search-input" placeholder="Search products...">
        </div>
        
        <div class="product-grid-container" id="product-grid">
            @foreach ($products as $product)
                @php
                    $stockClass = 'in-stock';
                    if($product->stock <= 0) {
                        $stockClass = 'out-of-stock';
                    } elseif($product->stock <= 10) {
                        $stockClass = 'low-stock';
                    }
                @endphp
                <div class="product-card" 
                     data-id="{{ $product->id }}"
                     data-name="{{ strtolower($product->name) }}"
                     data-category="{{ $product->category_id }}">
                    @if ($product->image && file_exists(public_path('storage/' . $product->image)))
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                    @else
                        <img src="{{ asset('images/no-image.png') }}" alt="{{ $product->name }}">
                    @endif
                    <div class="card-body">
                        <div class="product-name">{{ $product->name }}</div>
                        <div class="product-stock {{ $stockClass }}">
                            <i class="bi bi-{{ $stockClass == 'out-of-stock' ? 'x-circle' : ($stockClass == 'low-stock' ? 'exclamation-triangle' : 'check-circle') }}"></i>
                            {{ $product->stock }} {{ $product->unit }} available
                        </div>
                        <div class="product-price">{{ number_format($product->price, 2) }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    
    <!-- Cart Section -->
    <div class="cart-section">
        <div class="pos-header">
            <div class="cart-title">Order Summary</div>
        </div>
        
        <div class="cart-container">
            <div class="cart-header">
                <div>Items (<span id="cart-count">0</span>)</div>
                <div class="cart-clear" id="clear-cart">
                    <i class="bi bi-trash"></i> Clear All
                </div>
            </div>
            
            <div class="cart-items" id="cart-items">
                <div class="empty-state">
                    <i class="bi bi-cart"></i>
                    <p>No items added</p>
                </div>
            </div>
            
            <div class="summary-container">
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span id="subtotal">0.00</span>
                </div>
                <div class="summary-row">
                    <span>Discount:</span>
                    <span id="discount-amount">0.00</span>
                </div>
                <div class="summary-row">
                    <span>Tax (10%):</span>
                    <span id="tax">0.00</span>
                </div>
                <div class="summary-row summary-total">
                    <span>Total:</span>
                    <span id="total">0.00</span>
                </div>
            </div>
            
            <div class="discount-container">
                <label>Apply Discount</label>
                <div class="discount-input-group">
                    <select id="discount-type" class="discount-type">
                        <option value="none" selected>None</option>
                        <option value="fixed">Fixed</option>
                        <option value="percentage">Percentage</option>
                    </select>
                    <input type="number" id="discount-value" class="discount-value" placeholder="Value" disabled min="0" step="0.01">
                </div>
            </div>
            
            <div class="customer-select-container">
                <label for="customer-select">Customer</label>
                <select id="customer-select" class="customer-select">
                    <option value="" selected disabled>Select Customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="payment-methods-container">
                <label>Payment Method</label>
                <div class="payment-methods">
                    <div class="payment-btn" data-method="cash">
                        <i class="bi bi-cash-coin"></i>
                        <span>Cash</span>
                    </div>
                    <div class="payment-btn" data-method="credit_card">
                        <i class="bi bi-credit-card"></i>
                        <span>Card</span>
                    </div>
                    <div class="payment-btn" data-method="mobile_payment">
                        <i class="bi bi-phone"></i>
                        <span>Mobile</span>
                    </div>
                </div>
            </div>
            
            <button id="complete-sale" class="complete-sale-btn" disabled>
                <i class="bi bi-check-circle"></i> Complete Sale
            </button>
        </div>
    </div>
</div>

<!-- Receipt Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order Receipt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="receipt-content">
                <!-- Receipt content will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="print-receipt">
                    <i class="bi bi-printer"></i> Print Receipt
                </button>
            </div>
        </div>
    </div>
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">
 
<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/pos.js') }}"></script>
</body>
</html>