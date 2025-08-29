<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item">
            <div class="d-flex sidebar-profile">
                <div class="sidebar-profile-image">
                    @if (Auth::user()->profile_image)
                        <img src="{{ asset('template/images/profile-images/' . Auth::user()->profile_image) }}" class="rounded-circle" alt="User Profile Image" width="150" height="150">
                    @else
                        <img src="{{ asset('template/images/profile-images/avatar7.png') }}" alt="Admin" class="rounded-circle" width="150">
                    @endif
                    <span class="sidebar-status-indicator"></span>
                </div>
                <div class="sidebar-profile-name">
                    <p class="sidebar-name">{{ Auth::user()->name }}</p>
                    <p class="sidebar-designation">Welcome</p>
                </div>
            </div>
            <div class="nav-search">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Type to search..." aria-label="search" aria-describedby="search">
                    <div class="input-group-append">
                        <span class="input-group-text" id="search">
                            <i class="typcn typcn-zoom"></i>
                        </span>
                    </div>
                </div>
            </div>
            <p class="sidebar-menu-title">Dash menu</p>
        </li>

        <!-- Dashboard -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <i class="typcn typcn-device-desktop menu-icon"></i>
                <span class="menu-title">Dashboard <span class="badge badge-primary ml-3">New</span></span>
            </a>
        </li>

        <!-- User Section - Only for Super Admin and Admin -->
        @if(auth()->user()->hasRole(['super_admin', 'admin']))
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#user-basic" aria-expanded="false" aria-controls="user-basic">
                <i class="typcn typcn-briefcase menu-icon"></i>
                <span class="menu-title">User Management</span>
                <i class="typcn typcn-chevron-right menu-arrow"></i>
            </a>
            <div class="collapse" id="user-basic">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link " href="{{ route('users') }}">Users Listing</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('users.create') }}">Create User</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.approvals.pending') }}">Pending Approvals</a></li>
                    @if(auth()->user()->hasRole('super_admin'))
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.user_roles_permissions.index') }}">Roles & Permissions</a></li>
                    @endif
                </ul>
            </div>
        </li>
        @endif

        <!-- SaaS Management - Super Admin Only -->
        @if(auth()->user()->hasRole('super_admin'))
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#saas-menu" aria-expanded="false" aria-controls="saas-menu">
                <i class="typcn typcn-cog menu-icon"></i>
                <span class="menu-title">SaaS Management</span>
                <i class="typcn typcn-chevron-right menu-arrow"></i>
            </a>
            <div class="collapse" id="saas-menu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.tenants.index') }}">Tenants</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.subscriptions.index') }}">Subscriptions</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('pricing.index') }}">Pricing Plans</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.pos-settings.index') }}">POS Settings</a></li>
                </ul>
            </div>
        </li>
        @endif

        <!-- Project Settings - Only for Admin -->
        @if(auth()->user()->hasRole('admin'))
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.project.settings.index') }}">
                <i class="typcn typcn-cog menu-icon"></i>
                <span class="menu-title">Project Settings</span>
            </a>
        </li>
        @endif

        <!-- Business Sections - Hidden from Super Admin -->
        @if(!auth()->user()->hasRole('super_admin'))
        
        <!-- Brands Section -->
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#brands-basic" aria-expanded="false" aria-controls="brands-basic">
                <i class="typcn typcn-briefcase menu-icon"></i>
                <span class="menu-title">Brands</span>
                <i class="typcn typcn-chevron-right menu-arrow"></i>
            </a>
            <div class="collapse" id="brands-basic">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link" href="{{ route('brands') }}">Brands Listing</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('brands.create') }}">Create Brand</a></li>
                </ul>
            </div>
        </li>

        <!-- Products Section -->
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#product-menu" aria-expanded="false" aria-controls="product-menu">
                <i class="typcn typcn-shopping-cart menu-icon"></i>
                <span class="menu-title">Products</span>
                <i class="typcn typcn-chevron-right menu-arrow"></i>
            </a>
            <div class="collapse" id="product-menu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}">All Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('products.create') }}">Add Product</a></li>
                </ul>
            </div>
        </li>

        <!-- Categories Section -->
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#category-menu" aria-expanded="false" aria-controls="category-menu">
                <i class="typcn typcn-th-menu menu-icon"></i>
                <span class="menu-title">Categories</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="category-menu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link  ajax-link" href="{{ route('categories') }}">All Categories</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('categories.create') }}">Add Category</a></li>
                </ul>
            </div>
        </li>

        <!-- Warehouses Section -->
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#warehouse-menu" aria-expanded="false" aria-controls="warehouse-menu">
                <i class="typcn typcn-home menu-icon"></i>
                <span class="menu-title">Warehouses</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="warehouse-menu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link" href="{{ route('warehouses') }}">All Warehouses</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('warehouses.create') }}">Add Warehouse</a></li>
                </ul>
            </div>
        </li>

        <!-- Customers Section -->
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#customer-menu" aria-expanded="false" aria-controls="customer-menu">
                <i class="typcn typcn-group menu-icon"></i>
                <span class="menu-title">Customers</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="customer-menu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link" href="{{ route('customers') }}">All Customers</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('customers.create') }}">Add Customer</a></li>
                </ul>
            </div>
        </li>

        <!-- POS System -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('pos') }}" target="_blank">
                <i class="typcn typcn-calculator menu-icon"></i>
                <span class="menu-title">POS System <span class="badge badge-success ml-2">Live</span></span>
            </a>
        </li>

        <!-- Sales Section -->
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#sales-menu" aria-expanded="false" aria-controls="sales-menu">
                <i class="typcn typcn-chart-bar menu-icon"></i>
                <span class="menu-title">Sales</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="sales-menu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.sales.index') }}">All Sales</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.sales.create') }}">Add Sale</a></li>
                </ul>
            </div>
        </li>

        <!-- Purchase Section -->
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#purchase-basic" aria-expanded="false" aria-controls="purchase-basic">
                <i class="typcn typcn-shopping-cart menu-icon"></i>
                <span class="menu-title">Purchases</span>
                <i class="typcn typcn-chevron-right menu-arrow"></i>
            </a>
            <div class="collapse" id="purchase-basic">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.purchases.index') }}">Purchase Listing</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.purchases.create') }}">Create Purchase</a>
                    </li>
                </ul>
            </div>
        </li>

        <!-- Reports Section -->
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#reports-menu" aria-expanded="false" aria-controls="reports-menu">
                <i class="typcn typcn-chart-line menu-icon"></i>
                <span class="menu-title">Reports</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="reports-menu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link" href="{{ route('reports.index') }}">Reports Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('reports.sales') }}">Sales Reports</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('reports.products') }}">Products Report</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('reports.customers') }}">Customers Report</a></li>
                </ul>
            </div>
        </li>

        <!-- POS Settings -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('pos-settings.index') }}">
                <i class="typcn typcn-cog menu-icon"></i>
                <span class="menu-title">POS Settings</span>
            </a>
        </li>

        <!-- Notifications Section -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('notifications.index') }}">
                <i class="typcn typcn-bell menu-icon"></i>
                <span class="menu-title">Notifications</span>
                <span class="badge badge-danger ml-2" id="sidebar-notification-count" style="display: none;">0</span>
            </a>
        </li>

        <!-- Expenses Category Section -->
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#expense-category-menu" aria-expanded="false" aria-controls="expense-category-menu">
                <i class="typcn typcn-folder menu-icon"></i>
                <span class="menu-title">Expense Categories</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="expense-category-menu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link" href="{{ route('expense-categories') }}">All Expense Categories</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('expense-categories.create') }}">Add Expense Category</a></li>
                </ul>
            </div>
        </li>

        <!-- Expenses Section -->
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#expenses-menu" aria-expanded="false" aria-controls="expenses-menu">
                <i class="typcn typcn-credit-card menu-icon"></i>
                <span class="menu-title">Expenses</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="expenses-menu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link" href="{{ route('expenses') }}">All Expenses</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('expenses.create') }}">Add Expense</a></li>
                </ul>
            </div>
        </li>

        <!-- Barcode Management Section -->
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#barcode-menu" aria-expanded="false" aria-controls="barcode-menu">
                <i class="fas fa-barcode menu-icon"></i>
                <span class="menu-title">Barcodes</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="barcode-menu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link" href="{{ route('barcodes.generate') }}">Generate Barcode</a></li>
                </ul>
            </div>
        </li>

        <!-- Quotations Section -->
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#quotation-basic" aria-expanded="false" aria-controls="quotation-basic">
                <i class="typcn typcn-document-text menu-icon"></i>
                <span class="menu-title">Quotations</span>
                <i class="typcn typcn-chevron-right menu-arrow"></i>
            </a>
            <div class="collapse" id="quotation-basic">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link" href="{{ route('quotations') }}">Quotation Listing</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('quotations.create') }}">Create Quotation</a></li>
                </ul>
            </div>
        </li>

        @endif

    </ul>
</nav>    <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="sales-menu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.sales.index') }}">All Sales</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.sales.create') }}">Add Sale</a></li>
                </ul>
            </div>
        </li>

        <!-- Purchase Section -->
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#purchase-basic" aria-expanded="false" aria-controls="purchase-basic">
                <i class="typcn typcn-shopping-cart menu-icon"></i>
                <span class="menu-title">Purchases</span>
                <i class="typcn typcn-chevron-right menu-arrow"></i>
            </a>
            <div class="collapse" id="purchase-basic">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.purchases.index') }}">Purchase Listing</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.purchases.create') }}">Create Purchase</a>
                    </li>
                </ul>
            </div>
        </li>

        <!-- Reports Section -->
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#reports-menu" aria-expanded="false" aria-controls="reports-menu">
                <i class="typcn typcn-chart-line menu-icon"></i>
                <span class="menu-title">Reports</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="reports-menu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link" href="{{ route('reports.index') }}">Reports Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('reports.sales') }}">Sales Reports</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('reports.products') }}">Products Report</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('reports.customers') }}">Customers Report</a></li>
                </ul>
            </div>
        </li>

        <!-- POS Settings -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('pos-settings.index') }}">
                <i class="typcn typcn-cog menu-icon"></i>
                <span class="menu-title">POS Settings</span>
            </a>
        </li>

        <!-- Notifications Section -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('notifications.index') }}">
                <i class="typcn typcn-bell menu-icon"></i>
                <span class="menu-title">Notifications</span>
                <span class="badge badge-danger ml-2" id="sidebar-notification-count" style="display: none;">0</span>
            </a>
        </li>

        <!-- Expenses Category Section -->
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#expense-category-menu" aria-expanded="false" aria-controls="expense-category-menu">
                <i class="typcn typcn-folder menu-icon"></i>
                <span class="menu-title">Expense Categories</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="expense-category-menu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link" href="{{ route('expense-categories') }}">All Expense Categories</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('expense-categories.create') }}">Add Expense Category</a></li>
                </ul>
            </div>
        </li>

        <!-- Expenses Section -->
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#expenses-menu" aria-expanded="false" aria-controls="expenses-menu">
                <i class="typcn typcn-credit-card menu-icon"></i>
                <span class="menu-title">Expenses</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="expenses-menu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link" href="{{ route('expenses') }}">All Expenses</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('expenses.create') }}">Add Expense</a></li>
                </ul>
            </div>
        </li>

        <!-- Barcode Management Section -->
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#barcode-menu" aria-expanded="false" aria-controls="barcode-menu">
                <i class="fas fa-barcode menu-icon"></i>
                <span class="menu-title">Barcodes</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="barcode-menu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link" href="{{ route('barcodes.generate') }}">Generate Barcode</a></li>
                </ul>
            </div>
        </li>

        <!-- Quotations Section -->
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#quotation-basic" aria-expanded="false" aria-controls="quotation-basic">
                <i class="typcn typcn-document-text menu-icon"></i>
                <span class="menu-title">Quotations</span>
                <i class="typcn typcn-chevron-right menu-arrow"></i>
            </a>
            <div class="collapse" id="quotation-basic">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link" href="{{ route('quotations') }}">Quotation Listing</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('quotations.create') }}">Create Quotation</a></li>
                </ul>
            </div>
        </li>

        @endif

    </ul>
</nav>