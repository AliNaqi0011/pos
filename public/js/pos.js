// Initialize variables
let cart = [];
let discount = { type: 'none', value: 0 };
let paymentMethod = null;
let selectedCustomerId = null;
let receiptModal = null;
let activeCategory = 'all';
let searchQuery = '';

// Set up CSRF token for AJAX requests
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// Initialize when DOM is loaded
$(document).ready(function() {
    initializePOS();
});

function initializePOS() {
    // Initialize modal
    receiptModal = new bootstrap.Modal(document.getElementById('receiptModal'));
    
    // Update time every second
    updateCurrentTime();
    setInterval(updateCurrentTime, 1000);
    
    // Set up event listeners
    setupEventListeners();
    
    // Add click handlers to product cards
    $('.product-card').on('click', function() {
        const productId = $(this).data('id');
        const productName = $(this).data('name');
        const productPrice = parseFloat($(this).find('.product-price').text().replace(/[^0-9.]/g, ''));
        const productUnit = $(this).find('.product-stock').text().split(' ').slice(-2)[0];
        const productImage = $(this).find('img').attr('src');
        const productStock = parseInt($(this).find('.product-stock').text().split(' ')[0]);
        
        addToCart(productId, productName, productPrice, productUnit, productImage, productStock);
    });
}

// Update current time display
function updateCurrentTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    const dateString = now.toLocaleDateString([], { weekday: 'short', month: 'short', day: 'numeric' });
    $('#current-time').text(`${dateString}, ${timeString}`);
}

// Set up all event listeners
function setupEventListeners() {
    // Category filter
    $('.category-btn').on('click', function() {
        $('.category-btn').removeClass('active');
        $(this).addClass('active');
        activeCategory = $(this).data('category');
        filterProducts();
    });
    
    // Search input
    $('#product-search').on('input', function() {
        searchQuery = $(this).val().toLowerCase();
        filterProducts();
    });
    
    // Discount type change
    $('#discount-type').on('change', function() {
        const discountValueEl = $('#discount-value');
        if ($(this).val() === 'none') {
            discountValueEl.prop('disabled', true);
            discountValueEl.val('');
        } else {
            discountValueEl.prop('disabled', false);
        }
        applyDiscount();
    });
    
    // Discount value change
    $('#discount-value').on('input', applyDiscount);
    
    // Clear cart
    $('#clear-cart').on('click', clearCart);
    
    // Print receipt
    $('#print-receipt').on('click', printReceipt);
    
    // Customer select change
    $('#customer-select').on('change', function() {
        selectedCustomerId = $(this).val();
    });
    
    // Payment method selection
    $('.payment-btn').on('click', function(e) {
        e.stopPropagation();
        $('.payment-btn').removeClass('active');
        $(this).addClass('active');
        paymentMethod = $(this).data('method');
    });
    
    // Complete sale button
    $('#complete-sale').on('click', completeSale);
}

// Filter products by category and search query
function filterProducts() {
    $('.product-card').each(function() {
        const matchesCategory = activeCategory === 'all' || $(this).data('category') == activeCategory;
        const matchesSearch = $(this).data('name').includes(searchQuery);
        
        if (matchesCategory && matchesSearch) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
}

// Add product to cart with stock validation
function addToCart(id, name, price, unit, image, stock) {
    // Check stock before adding to cart
    if (stock <= 0) {
        Swal.fire({
            icon: 'error',
            title: 'Out of Stock',
            text: 'This product is currently out of stock',
            timer: 2000,
            showConfirmButton: false
        });
        return;
    }

    const existingItem = cart.find(item => item.id === id);
    
    if (existingItem) {
        // Check if adding more exceeds available stock
        if (existingItem.quantity + 1 > stock) {
            Swal.fire({
                icon: 'warning',
                title: 'Stock Limit',
                text: `Only ${stock} units available for this product`,
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }
        existingItem.quantity += 1;
    } else {
        cart.push({
            id: id,
            name: name,
            price: price,
            unit: unit,
            image: image,
            stock: stock,
            quantity: 1
        });
    }
    
    updateCartDisplay();
}

// Update cart display with stock indicators
function updateCartDisplay() {
    const cartItemsEl = $('#cart-items');
    const cartCountEl = $('#cart-count');
    const completeSaleBtn = $('#complete-sale');
    
    if (cart.length === 0) {
        cartItemsEl.html(`
            <div class="empty-state">
                <i class="bi bi-cart"></i>
                <p>No items added</p>
            </div>`);
        completeSaleBtn.prop('disabled', true);
    } else {
        let html = '';
        cart.forEach((item, index) => {
            const itemTotal = item.price * item.quantity;
            const remainingStock = item.stock - item.quantity;
            const stockStatusClass = remainingStock <= 0 ? 'text-danger' : 
                                  (remainingStock <= 5 ? 'text-warning' : 'text-success');
            
            html += `
                <div class="cart-item fade-in">
                    <img src="${item.image}" alt="${item.name}" class="cart-item-img">
                    <div class="cart-item-details">
                        <div class="cart-item-name">${item.name}</div>
                        <div class="cart-item-price">₹${item.price.toFixed(2)} ${item.unit}</div>
                        <small class="${stockStatusClass}">
                            <i class="bi ${remainingStock <= 0 ? 'bi-x-circle' : (remainingStock <= 5 ? 'bi-exclamation-triangle' : 'bi-check-circle')}"></i>
                            ${remainingStock} remaining
                        </small>
                    </div>
                    <div class="cart-item-qty">
                        <button class="qty-btn" onclick="updateQuantity(${index}, ${item.quantity - 1})">-</button>
                        <input type="number" class="qty-input" value="${item.quantity}" min="1" 
                               onchange="updateQuantity(${index}, this.value)" max="${item.stock}">
                        <button class="qty-btn" onclick="updateQuantity(${index}, ${item.quantity + 1})">+</button>
                    </div>
                    <div class="cart-item-total">₹${itemTotal.toFixed(2)}</div>
                    <div class="cart-item-remove" onclick="removeFromCart(${index})">
                        <i class="bi bi-trash"></i>
                    </div>
                </div>`;
        });
        cartItemsEl.html(html);
        completeSaleBtn.prop('disabled', false);
    }
    
    cartCountEl.text(cart.reduce((sum, item) => sum + item.quantity, 0));
    calculateTotals();
}

// Update item quantity in cart with stock validation
function updateQuantity(index, newQuantity) {
    newQuantity = parseInt(newQuantity);
    if (isNaN(newQuantity)) newQuantity = 1;
    if (newQuantity < 1) newQuantity = 1;
    
    // Check stock before updating quantity
    if (newQuantity > cart[index].stock) {
        Swal.fire({
            icon: 'warning',
            title: 'Stock Limit',
            text: `Only ${cart[index].stock} units available for this product`,
            timer: 2000,
            showConfirmButton: false
        });
        return;
    }
    
    cart[index].quantity = newQuantity;
    updateCartDisplay();
}

// Remove item from cart
function removeFromCart(index) {
    cart.splice(index, 1);
    updateCartDisplay();
}

// Clear entire cart with confirmation
function clearCart() {
    if (cart.length === 0) return;
    
    Swal.fire({
        title: 'Clear Cart?',
        text: 'Are you sure you want to remove all items from the cart?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Clear'
    }).then((result) => {
        if (result.isConfirmed) {
            cart = [];
            updateCartDisplay();
        }
    });
}

// Calculate order totals
function calculateTotals() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    let discountAmount = 0;
    
    // Apply discount
    if (discount.type === 'fixed') {
        discountAmount = Math.min(discount.value, subtotal);
    } else if (discount.type === 'percentage') {
        discountAmount = subtotal * (discount.value / 100);
    }
    
    const taxableAmount = subtotal - discountAmount;
    const tax = taxableAmount * 0.1; // 10% tax
    const total = taxableAmount + tax;
    
    // Update display
    $('#subtotal').text(subtotal.toFixed(2));
    $('#discount-amount').text(discountAmount.toFixed(2));
    $('#tax').text(tax.toFixed(2));
    $('#total').text(total.toFixed(2));
}

// Apply discount to order
function applyDiscount() {
    const type = $('#discount-type').val();
    const value = parseFloat($('#discount-value').val()) || 0;
    
    discount = { type, value };
    calculateTotals();
}

// Complete sale with validation
function completeSale() {
    // Validate
    if (!paymentMethod) {
        Swal.fire({
            icon: 'error',
            title: 'Payment Required',
            text: 'Please select a payment method',
            timer: 2000,
            showConfirmButton: false
        });
        return;
    }
    
    if (!selectedCustomerId) {
        Swal.fire({
            icon: 'error',
            title: 'Customer Required',
            text: 'Please select a customer',
            timer: 2000,
            showConfirmButton: false
        });
        return;
    }
    
    if (cart.length === 0) {
        Swal.fire({
            icon: 'error',
            title: 'Empty Cart',
            text: 'Please add items to the cart',
            timer: 2000,
            showConfirmButton: false
        });
        return;
    }
    
    // Check stock availability for all items
    const outOfStockItems = cart.filter(item => item.quantity > item.stock);
    if (outOfStockItems.length > 0) {
        Swal.fire({
            icon: 'error',
            title: 'Stock Issues',
            html: `Some items in your cart exceed available stock:<br>
                  ${outOfStockItems.map(item => `${item.name} (${item.stock} available)`).join('<br>')}`,
            confirmButtonText: 'OK'
        });
        return;
    }
    
    const subtotal = parseFloat($('#subtotal').text());
    const total = parseFloat($('#total').text());
    
    // Prepare order data
    const orderData = {
        customer_id: selectedCustomerId,
        items: cart,
        discount: discount,
        tax: parseFloat($('#tax').text()),
        total_items: cart.reduce((sum, item) => sum + item.quantity, 0),
        total_amount: subtotal,
        final_total: total,
        payment_method: paymentMethod
    };
    
    // Show loading
    Swal.fire({
        title: 'Processing Order',
        html: 'Please wait...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Send to server
    $.ajax({
        url: "/pos/checkout",
        method: "POST",
        data: orderData,
        success: function (response) {
            Swal.close();
            
            // Generate receipt
            generateReceipt(orderData, response.order_id);
            
            // Show receipt modal
            receiptModal.show();
            
            // Reset POS for next sale
            resetPOS();
        },
        error: function (xhr) {
            Swal.close();
            let errMsg = 'Failed to process the order.';
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                errMsg = Object.values(xhr.responseJSON.errors).flat().join(' ');
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errMsg = xhr.responseJSON.message;
            }
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errMsg
            });
            
            if (xhr.status === 419) { // CSRF token mismatch
                console.error('CSRF token mismatch. Current token:', $('meta[name="csrf-token"]').attr('content'));
            }
        }
    });
}

// Generate receipt HTML
function generateReceipt(orderData, orderId) {
    const now = new Date();
    const receiptEl = $('#receipt-content');
    
    let itemsHtml = '';
    orderData.items.forEach(item => {
        itemsHtml += `
            <tr>
                <td>${item.name}</td>
                <td>${item.quantity} ${item.unit}</td>
                <td>₹${item.price.toFixed(2)}</td>
                <td>₹${(item.price * item.quantity).toFixed(2)}</td>
            </tr>`;
    });
    
    receiptEl.html(`
        <div class="text-center mb-4">
            <h4>Your Store Name</h4>
            <p>123 Main Street, City</p>
            <p>Tel: (123) 456-7890</p>
        </div>
        
        <div class="d-flex justify-content-between mb-3">
            <div>
                <strong>Receipt #:</strong> ${orderId}
            </div>
            <div>
                <strong>Date:</strong> ${now.toLocaleDateString()}
            </div>
            <div>
                <strong>Time:</strong> ${now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
            </div>
        </div>
        
        <div class="mb-3">
            <strong>Customer:</strong> 
            Customer #${orderData.customer_id}
        </div>
        
        <hr>
        
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                ${itemsHtml}
            </tbody>
        </table>
        
        <hr>
        
        <div class="text-end">
            <p><strong>Subtotal:</strong> ₹${orderData.total_amount.toFixed(2)}</p>
            <p><strong>Discount:</strong> ₹${orderData.discount.value.toFixed(2)}</p>
            <p><strong>Tax (10%):</strong> ₹${orderData.tax.toFixed(2)}</p>
            <h5><strong>Total:</strong> ₹${orderData.final_total.toFixed(2)}</h5>
        </div>
        
        <div class="mt-4">
            <p><strong>Payment Method:</strong> ${getPaymentMethodName(orderData.payment_method)}</p>
            <p>Thank you for your purchase!</p>
        </div>`);
}

// Get payment method display name
function getPaymentMethodName(method) {
    switch(method) {
        case 'cash': return 'Cash';
        case 'credit_card': return 'Credit Card';
        case 'mobile_payment': return 'Mobile Payment';
        default: return method;
    }
}

// Print receipt
function printReceipt() {
    const receiptContent = $('#receipt-content').html();
    const originalContent = $('body').html();
    
    $('body').html(`
        <div style="width:80mm; margin:0 auto; padding:10px; font-family:Arial, sans-serif;">
            ${receiptContent}
        </div>`);
    
    window.print();
    $('body').html(originalContent);
    receiptModal.hide();
}

// Reset POS for new sale
function resetPOS() {
    cart = [];
    discount = { type: 'none', value: 0 };
    paymentMethod = null;
    selectedCustomerId = null;
    
    // Reset UI
    $('#discount-type').val('none');
    $('#discount-value').val('');
    $('#discount-value').prop('disabled', true);
    $('.payment-btn').removeClass('active');
    $('#customer-select').val('');
    
    updateCartDisplay();
}