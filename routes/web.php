<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    BlogController,
    PlanController,
    DashboardController,
    RoleDashboardController,
    VerificationController,
    UserProfileController,
    UserSettingsController,
    ProfileController,
    SocialiteController,
    UserController,
    CategoryController,
    BrandController,
    WarehouseController,
    ProductController,
    SaleController,
    SalePaymentController,
    CustomerController,
    BarcodeController,
    POSController,
    QuotationController,
    ExpenseCategoryController,
    ExpenseController,
    UserRoleController,
    PurchaseController,
    SubscriptionController,
    SaasController,
    ApprovalController,
    TenantController,
    PricingController
};

Route::get('/', function () {
    return view('auth.login');
});


Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// Public Routes
Route::get('/', fn() => view('auth.login'));
Route::get('/google/login', [SocialiteController::class, 'googleLogin'])->name('google.login');
Route::get('/auth/google/callback', [SocialiteController::class, 'googleCallback'])->name('google.callback');

Route::get('/login/facebook', [SocialiteController::class, 'redirectToFacebookProvider'])->name('facebook.login');
Route::get('/login/facebook/callback', [SocialiteController::class, 'handleFacebookProviderCallback'])->name('facebook.callback');

Route::get('/verify', [VerificationController::class, 'verifyUser'])->name('verify.user');

// Routes with session check middleware
Route::middleware(['check.session.data'])->group(function () {
    Route::get('/verification', [VerificationController::class, 'verification'])->name('verification');
    Route::post('/verification', [VerificationController::class, 'VerifyEmaiLPhoneOtp'])->name('verify.email.phone.otp');

    Route::get('/2fa-verification', [VerificationController::class, 'two_fa_verification'])->name('two.fa.verification');
    Route::post('/verify-2fa-vemail-otp', [VerificationController::class, 'verify_two_fa_email_otp'])->name('verify.email.otp.two.fa');

    Route::get('/2fa-phone-verification', [VerificationController::class, 'two_fa_phone_verification'])->name('two.fa.phone.verification');
    Route::post('/verify-2fa-phone-otp', [VerificationController::class, 'verify_two_fa_phone_otp'])->name('verify.phone.otp.two.fa');
});

// Status message routes (no middleware)
Route::middleware(['auth'])->group(function () {
    Route::get('/approval/pending', function() {
        return view('admin.approvals.status-messages');
    })->name('approval.pending.message');
    
    Route::get('/approval/rejected', function() {
        return view('admin.approvals.status-messages');
    })->name('approval.rejected.message');
    
    Route::get('/payment/verification/pending', function() {
        return view('admin.approvals.status-messages');
    })->name('payment.verification.pending');
});

// Auth & Email Verified Middleware

Route::middleware(['auth','role:super_admin|admin|seller|manager|sales', 'verified', 'check.email.verify', 'data.isolation'])->group(function () {
    // 
// Main view route (unchanged)
Route::get('/admin/user-roles-permissions', [UserRoleController::class, 'index'])
     ->name('admin.user_roles_permissions.index');

// Updated routes for new functionality
Route::get('/admin/user-roles-permissions/permissions/{user}', [UserRoleController::class, 'getUserRoles'])
     ->name('admin.user_roles_permissions.get_permissions');

Route::post('/admin/user-roles-permissions/update-role-permissions', [UserRoleController::class, 'updateRolePermissions'])
     ->name('admin.user_roles_permissions.update_role_permissions');

Route::post('/admin/user-roles-permissions/assign-roles', [UserRoleController::class, 'assignRolesToUser'])
     ->name('admin.user_roles_permissions.assign_roles');

// Keeping old routes for backward compatibility (can be removed after updating all frontend calls)
Route::post('/admin/user-roles-permissions/assign-role', [UserRoleController::class, 'assignRole'])
     ->name('admin.user_roles_permissions.assign_role');

Route::post('/admin/user-roles-permissions/assign-permissions', [UserRoleController::class, 'assignPermissions'])
     ->name('admin.user_roles_permissions.assign_permissions');
    Route::get('/dashboard', [RoleDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [RoleDashboardController::class, 'stats'])->name('dashboard.stats');

    // Verification
    Route::get('/email/verify', [VerificationController::class, 'emailVerifyUser'])->name('email.verification.notice');
    Route::post('/resend/email/verify', [VerificationController::class, 'resendEmailVerifyUser'])->name('verification.resend');
    Route::post('/phone/verification', [VerificationController::class, 'phone_verification'])->name('phone.verifications');
    Route::post('/phone/verify', [VerificationController::class, 'check_phone_verification'])->name('check.phone.otp');

    // Plans
    Route::get('plans', [PlanController::class, 'index'])->name('plans');
    Route::get('plans/{plan}', [PlanController::class, 'show'])->name("plans.show");
    Route::post('subscription', [PlanController::class, 'subscription'])->name("subscription.create");
    
    // Subscriptions
    Route::prefix('subscriptions')->group(function () {
        Route::get('/', [SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::post('/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscriptions.subscribe');
        Route::post('/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
        Route::post('/resume', [SubscriptionController::class, 'resume'])->name('subscriptions.resume');
        Route::post('/change-plan', [SubscriptionController::class, 'changePlan'])->name('subscriptions.change-plan');
        Route::get('/invoices', [SubscriptionController::class, 'invoices'])->name('subscriptions.invoices');
        Route::get('/invoices/{invoice}', [SubscriptionController::class, 'downloadInvoice'])->name('subscriptions.download-invoice');
    });
    
    // SaaS Management
    Route::prefix('saas')->group(function () {
        Route::get('/setup', [SaasController::class, 'setupTenant'])->name('saas.setup');
        Route::post('/create-tenant', [SaasController::class, 'createTenant'])->name('saas.create-tenant');
        Route::get('/tenant-dashboard', [SaasController::class, 'tenantDashboard'])->name('saas.tenant-dashboard');
        Route::get('/manage-users', [SaasController::class, 'manageUsers'])->name('saas.manage-users');
        Route::post('/invite-user', [SaasController::class, 'inviteUser'])->name('saas.invite-user');
        Route::get('/subscription-status', [SaasController::class, 'subscriptionStatus'])->name('saas.subscription-status');
        Route::post('/upgrade-subscription', [SaasController::class, 'upgradeSubscription'])->name('saas.upgrade-subscription');
        Route::get('/tenant-settings', [SaasController::class, 'tenantSettings'])->name('saas.tenant-settings');
        Route::post('/update-tenant-settings', [SaasController::class, 'updateTenantSettings'])->name('saas.update-tenant-settings');
    });

    // User Management
    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users');
        Route::get('/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/store', [UserController::class, 'store'])->name('users.store');
        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('users.edit');
        Route::post('/update', [UserController::class, 'update'])->name('users.update');
        Route::delete('/delete/{id}', [UserController::class, 'delete'])->name('users.delete');

        // Profile
        Route::get('/profile', [UserProfileController::class, 'index'])->name('user.profile');
        Route::get('/profile/edit/{id}', [UserProfileController::class, 'edit'])->name('user.profile.edit');
        Route::post('/profile/update/{id}', [UserProfileController::class, 'update'])->name('user.profile.update');

        // Social Profiles
        Route::get('/profile/social/create', [UserProfileController::class, 'SocialCreate'])->name('user.profile.social.create');
        Route::post('/profile/social/store', [UserProfileController::class, 'SocialStore'])->name('user.profile.social.store');
        Route::get('/profile/social/edit/{id}', [UserProfileController::class, 'SocialEdit'])->name('user.profile.social.edit');
        Route::post('/profile/social/update/{id}', [UserProfileController::class, 'SocialUpdate'])->name('user.profile.social.update');

        // Settings
        Route::get('/settings', [UserSettingsController::class, 'index'])->name('user.settings');
        Route::get('/settings/create', [UserSettingsController::class, 'create'])->name('user.settings.create');
        Route::post('/settings/store', [UserSettingsController::class, 'store'])->name('user.settings.store');
        Route::get('/settings/edit/{id}', [UserSettingsController::class, 'edit'])->name('user.settings.edit');
        Route::post('/settings/update/{id}', [UserSettingsController::class, 'update'])->name('user.settings.update');
        Route::post('/store-radio-value', [UserSettingsController::class, 'twoFaStore'])->name('user.profile.2fa.store');
        Route::get('/get-radio-value', [UserSettingsController::class, 'get'])->name('get.user.profile.2fa.store');
    });

    // Blog
    Route::prefix('blog')->group(function () {
        Route::get('/listing', [BlogController::class, 'listing'])->name('blog.listing');
        Route::get('/create', [BlogController::class, 'create'])->name('blog.create');
        Route::post('/store', [BlogController::class, 'store'])->name('blog.store');
        Route::get('/edit/{id}', [BlogController::class, 'edit'])->name('blog.edit');
        Route::post('/update', [BlogController::class, 'update'])->name('blog.update');
        Route::delete('/delete/{id}', [BlogController::class, 'delete'])->name('blog.delete');
    });

    // POS
    Route::get('pos', [POSController::class, 'index'])->name('pos');
    Route::get('pos/filter-products', [POSController::class, 'filterProducts'])->name('pos.filterProducts');
    Route::post('pos/add-to-cart', [POSController::class, 'addToCart'])->name('pos.addToCart');
    Route::post('pos/checkout', [POSController::class, 'checkout'])->name('pos.checkout');

    // Sales List
    Route::get('sales', [POSController::class, 'salesList'])->name('sales.list');
    Route::get('sales/{saleId}', [POSController::class, 'saleDetails'])->name('sales.details');

    // Categories
    Route::get('categories', [CategoryController::class, 'index'])->name('categories');
    Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('categories/store', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('categories/edit/{id}', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::post('categories/update', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/delete/{id}', [CategoryController::class, 'delete'])->name('categories.delete');

    // Products
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('products/store', [ProductController::class, 'store'])->name('products.store');
    Route::get('products/edit/{id}', [ProductController::class, 'edit'])->name('products.edit');
    Route::post('products/update', [ProductController::class, 'update'])->name('products.update');
    Route::delete('products/delete/{id}', [ProductController::class, 'delete'])->name('products.delete');

    // Brands
    Route::get('brands', [BrandController::class, 'index'])->name('brands');
    Route::get('brands/create', [BrandController::class, 'create'])->name('brands.create');
    Route::post('brands/store', [BrandController::class, 'store'])->name('brands.store');
    Route::get('brands/edit/{id}', [BrandController::class, 'edit'])->name('brands.edit');
    Route::post('brands/update', [BrandController::class, 'update'])->name('brands.update');
    Route::delete('brands/delete/{id}', [BrandController::class, 'delete'])->name('brands.delete');

    // Expenses
    Route::get('expenses', [ExpenseController::class, 'index'])->name('expenses');
    Route::get('expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
    Route::post('expenses/store', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('expenses/edit/{id}', [ExpenseController::class, 'edit'])->name('expenses.edit');
    Route::post('expenses/update', [ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('expenses/delete/{id}', [ExpenseController::class, 'delete'])->name('expenses.delete');

    // Expense Categories
    Route::get('expense-categories', [ExpenseCategoryController::class, 'index'])->name('expense-categories');
    Route::get('expense-categories/create', [ExpenseCategoryController::class, 'create'])->name('expense-categories.create');
    Route::post('expense-categories/store', [ExpenseCategoryController::class, 'store'])->name('expense-categories.store');
    Route::get('expense-categories/edit/{id}', [ExpenseCategoryController::class, 'edit'])->name('expense-categories.edit');
    Route::post('expense-categories/update', [ExpenseCategoryController::class, 'update'])->name('expense-categories.update');
    Route::delete('expense-categories/delete/{id}', [ExpenseCategoryController::class, 'delete'])->name('expense-categories.delete');

    // Customers
    Route::get('customers', [CustomerController::class, 'index'])->name('customers');
    Route::get('customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('customers/store', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('customers/edit/{id}', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::post('customers/update', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('customers/delete/{id}', [CustomerController::class, 'destroy'])->name('customers.delete');

    // Sales
    Route::prefix('admin/sales')->group(function () {
        Route::get('/', [SaleController::class, 'index'])->name('admin.sales.index');
        Route::get('/create', [SaleController::class, 'create'])->name('admin.sales.create');
        Route::post('/store', [SaleController::class, 'store'])->name('admin.sales.store');
        Route::get('/edit/{id}', [SaleController::class, 'edit'])->name('admin.sales.edit');
        Route::post('/update', [SaleController::class, 'update'])->name('admin.sales.update');
        Route::delete('/delete/{id}', [SaleController::class, 'destroy'])->name('admin.sales.delete');
        Route::get('/show/{id}', [SaleController::class, 'show'])->name('admin.sales.show');
        Route::get('/receipt/{id}', [SaleController::class, 'receipt'])->name('admin.sales.receipt');

        Route::get('/return-form/{id}', [SaleController::class, 'showReturnForm'])->name('admin.sales.returnForm');
        Route::post('/process-return/{sale}', [SaleController::class, 'processReturn'])->name('admin.sales.processReturn');
    });


// purchase 

    Route::prefix('admin/purchases')->group(function () {
    Route::get('/', [PurchaseController::class, 'index'])->name('admin.purchases.index');
    Route::get('/create', [PurchaseController::class, 'create'])->name('admin.purchases.create');
    Route::post('/store', [PurchaseController::class, 'store'])->name('admin.purchases.store');
    Route::get('/edit/{id}', [PurchaseController::class, 'edit'])->name('admin.purchases.edit'); // if you add edit later
    Route::post('/update', [PurchaseController::class, 'update'])->name('admin.purchases.update'); // if you add update later
    Route::delete('/delete/{id}', [PurchaseController::class, 'destroy'])->name('admin.purchases.delete');
    Route::get('/show/{id}', [PurchaseController::class, 'show'])->name('admin.purchases.show');

    // Purchase Return
    Route::get('/return-form/{id}', [PurchaseController::class, 'showReturnForm'])->name('admin.purchases.returnForm');
    Route::post('/process-return/{purchase}', [PurchaseController::class, 'processReturn'])->name('admin.purchases.processReturn');
});



    // Sale Payments
    Route::get('sales/{saleId}/payments', [SalePaymentController::class, 'showPayments'])->name('sales.payments.index');
    Route::get('sales/{saleId}/payments/create', [SalePaymentController::class, 'create'])->name('sales.payments.create');
    Route::post('sales/{saleId}/payments', [SalePaymentController::class, 'store'])->name('sales.payments.store');

    // Barcode
    Route::get('barcodes', [BarcodeController::class, 'index'])->name('barcodes.generate');
    Route::post('barcodes/print', [BarcodeController::class, 'print'])->name('barcodes.print');
    Route::get('barcodes/scan', [BarcodeController::class, 'scanProduct'])->name('barcodes.scan');

    // Quotations
    Route::get('quotations', [QuotationController::class, 'index'])->name('quotations');
    Route::get('quotations/create', [QuotationController::class, 'create'])->name('quotations.create');
    Route::post('quotations/store', [QuotationController::class, 'store'])->name('quotations.store');
    Route::get('quotations/edit/{id}', [QuotationController::class, 'edit'])->name('quotations.edit');
    Route::post('quotations/update', [QuotationController::class, 'update'])->name('quotations.update');
    Route::delete('quotations/delete/{id}', [QuotationController::class, 'delete'])->name('quotations.delete');

        
    Route::get('/warehouses', [WarehouseController::class, 'index'])->name('warehouses');
    Route::get('/warehouses/create', [WarehouseController::class, 'create'])->name('warehouses.create');
    Route::post('/warehouses', [WarehouseController::class, 'store'])->name('warehouses.store');
    Route::get('/warehouses/{id}/edit', [WarehouseController::class, 'edit'])->name('warehouses.edit');
    Route::put('/warehouses/{id}', [WarehouseController::class, 'update'])->name('warehouses.update');
    Route::delete('/warehouses/{id}', [WarehouseController::class, 'delete'])->name('warehouses.delete');
    
    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/count', [\App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('notifications.count');
        Route::get('/latest', [\App\Http\Controllers\NotificationController::class, 'getLatest'])->name('notifications.latest');
        Route::post('/mark-read/{id}', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
        Route::post('/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    });
    
    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
        Route::get('/sales', [\App\Http\Controllers\ReportController::class, 'salesReport'])->name('reports.sales');
        Route::get('/products', [\App\Http\Controllers\ReportController::class, 'productsReport'])->name('reports.products');
        Route::get('/customers', [\App\Http\Controllers\ReportController::class, 'customersReport'])->name('reports.customers');
    });
    
    // Inventory Management
    Route::prefix('inventory')->group(function () {
        Route::post('/fix', [\App\Http\Controllers\InventoryController::class, 'fixInventory'])->name('inventory.fix');
        Route::get('/validate-stock', [\App\Http\Controllers\InventoryController::class, 'validateStock'])->name('inventory.validate');
    });
    
    // POS Settings
    Route::prefix('pos-settings')->group(function () {
        Route::get('/', [\App\Http\Controllers\POSSettingsController::class, 'index'])->name('pos-settings.index');
        Route::post('/update', [\App\Http\Controllers\POSSettingsController::class, 'update'])->name('pos-settings.update');
        Route::get('/api', [\App\Http\Controllers\POSSettingsController::class, 'getSettings'])->name('pos-settings.api');
    });
    
    // Admin POS Settings (for super admin)
    Route::prefix('admin/pos-settings')->group(function () {
        Route::get('/', [\App\Http\Controllers\POSSettingsController::class, 'index'])->name('admin.pos-settings.index');
        Route::post('/update', [\App\Http\Controllers\POSSettingsController::class, 'update'])->name('admin.pos-settings.update');
    });
    
    // Approval Routes
    Route::prefix('admin/approvals')->group(function () {
        Route::get('/pending', [ApprovalController::class, 'pendingUsers'])->name('admin.approvals.pending');
        Route::post('/{user}/approve', [ApprovalController::class, 'approve'])->name('admin.approvals.approve');
        Route::post('/{user}/reject', [ApprovalController::class, 'reject'])->name('admin.approvals.reject');
        Route::post('/{user}/verify-payment', [ApprovalController::class, 'verifyPayment'])->name('admin.approvals.verify-payment');
    });
    
    // Tenant Routes
    Route::prefix('admin/tenants')->group(function () {
        Route::get('/', [TenantController::class, 'index'])->name('admin.tenants.index');
    });
    
    // Subscription Routes
    Route::prefix('admin/subscriptions')->group(function () {
        Route::get('/', [SubscriptionController::class, 'index'])->name('admin.subscriptions.index');
    });
    
    // Pricing Routes
    Route::get('/pricing', [PricingController::class, 'index'])->name('pricing.index');
    
    // Project Settings Routes
    Route::prefix('admin/project-settings')->group(function () {
        Route::get('/', [\App\Http\Controllers\ProjectSettingsController::class, 'index'])->name('admin.project.settings.index');
        Route::post('/update', [\App\Http\Controllers\ProjectSettingsController::class, 'update'])->name('admin.project.settings.update');
        Route::post('/test-fbr', [\App\Http\Controllers\ProjectSettingsController::class, 'testFBRConnection'])->name('admin.project.settings.test-fbr');
    });
    
    // Test Routes (Remove in production)
    Route::prefix('test')->group(function () {
        Route::get('/sales-returns', [\App\Http\Controllers\TestController::class, 'testSalesReturns']);
        Route::get('/purchases-returns', [\App\Http\Controllers\TestController::class, 'testPurchasesReturns']);
        Route::get('/product-stock', [\App\Http\Controllers\TestController::class, 'testProductStock']);
    });
    
    // System Health Check Routes (Remove in production)
    Route::prefix('system')->group(function () {
        Route::get('/health', [\App\Http\Controllers\SystemHealthController::class, 'checkSystem']);
        Route::post('/fix', [\App\Http\Controllers\SystemHealthController::class, 'fixCommonIssues']);
    });
});
require __DIR__.'/auth.php';
