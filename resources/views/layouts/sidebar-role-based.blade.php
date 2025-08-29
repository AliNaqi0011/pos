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
                    <p class="sidebar-designation">
                        @if(auth()->user()->hasRole('super_admin'))
                            Super Admin
                        @elseif(auth()->user()->hasRole('admin'))
                            Administrator
                        @elseif(auth()->user()->hasRole('seller'))
                            Seller
                        @else
                            User
                        @endif
                    </p>
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
            <p class="sidebar-menu-title">Main Menu</p>
        </li>

        <!-- Dashboard - Available to all -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <i class="typcn typcn-device-desktop menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>

        @if(auth()->user()->hasRole('super_admin'))
            <!-- Super Admin Only Sections -->
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#super-admin-menu" aria-expanded="false" aria-controls="super-admin-menu">
                    <i class="typcn typcn-cog menu-icon"></i>
                    <span class="menu-title">System Management</span>
                    <i class="typcn typcn-chevron-right menu-arrow"></i>
                </a>
                <div class="collapse" id="super-admin-menu">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.user_roles_permissions.index') }}">Roles & Permissions</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('plans') }}">Subscription Plans</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('saas.tenant-dashboard') }}">Tenant Management</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('subscriptions.index') }}">Subscriptions</a></li>
                    </ul>
                </div>
            </li>
        @endif

        @if(auth()->user()->hasRole(['super_admin', 'admin']))
            <!-- User Management - Super Admin and Admin -->
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#user-menu" aria-expanded="false" aria-controls="user-menu">
                    <i class="typcn typcn-group menu-icon"></i>
                    <span class="menu-title">User Management</span>
                    <i class="typcn typcn-chevron-right menu-arrow"></i>
                </a>
                <div class="collapse" id="user-menu">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"><a class="nav-link" href="{{ route('users') }}">All Users</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('users.create') }}">Add User</a></li>
                    </ul>
                </div>
            </li>

            <!-- Inventory Management -->
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#inventory-menu" aria-expanded="false" aria-controls="inventory-menu">
                    <i class="typcn typcn-shopping-cart menu-icon"></i>
                    <span class="menu-title">Inventory</span>
                    <i class="typcn typcn-chevron-right menu-arrow"></i>
                </a>
                <div class="collapse" id="inventory-menu">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}">Products</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('categories') }}">Categories</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('brands') }}">Brands</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('warehouses') }}">Warehouses</a></li>
                    </ul>
                </div>
            </li>

            <!-- Purchase Management -->
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#purchase-menu" aria-expanded="false" aria-controls="purchase-menu">
                    <i class="typcn typcn-input-checked menu-icon"></i>
                    <span class="menu-title">Purchases</span>
                    <i class="typcn typcn-chevron-right menu-arrow"></i>
                </a>
                <div class="collapse" id="purchase-menu">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.purchases.index') }}">All Purchases</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.purchases.create') }}">Add Purchase</a></li>
                    </ul>
                </div>
            </li>

            <!-- Expense Management -->
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#expense-menu" aria-expanded="false" aria-controls="expense-menu">
                    <i class="typcn typcn-credit-card menu-icon"></i>
                    <span class="menu-title">Expenses</span>
                    <i class="typcn typcn-chevron-right menu-arrow"></i>
                </a>
                <div class="collapse" id="expense-menu">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"><a class="nav-link" href="{{ route('expenses') }}">All Expenses</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('expense-categories') }}">Expense Categories</a></li>
                    </ul>
                </div>
            </li>
        @endif

        @if(auth()->user()->hasRole(['super_admin', 'admin', 'seller']))
            <!-- Sales Management - All roles -->
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#sales-menu" aria-expanded="false" aria-controls="sales-menu">
                    <i class="typcn typcn-chart-bar menu-icon"></i>
                    <span class="menu-title">Sales</span>
                    <i class="typcn typcn-chevron-right menu-arrow"></i>
                </a>
                <div class="collapse" id="sales-menu">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"><a class="nav-link" href="{{ route('pos') }}" target="_blank">POS System</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.sales.index') }}">All Sales</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.sales.create') }}">Manual Sale</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('quotations') }}">Quotations</a></li>
                    </ul>
                </div>
            </li>

            <!-- Customer Management -->
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#customer-menu" aria-expanded="false" aria-controls="customer-menu">
                    <i class="typcn typcn-user menu-icon"></i>
                    <span class="menu-title">Customers</span>
                    <i class="typcn typcn-chevron-right menu-arrow"></i>
                </a>
                <div class="collapse" id="customer-menu">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"><a class="nav-link" href="{{ route('customers') }}">All Customers</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('customers.create') }}">Add Customer</a></li>
                    </ul>
                </div>
            </li>
        @endif

        @if(auth()->user()->hasRole('seller'))
            <!-- Seller Quick Actions -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('pos') }}" target="_blank">
                    <i class="typcn typcn-calculator menu-icon"></i>
                    <span class="menu-title">POS System <span class="badge badge-success ml-2">Quick</span></span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="{{ route('products.index') }}">
                    <i class="typcn typcn-shopping-cart menu-icon"></i>
                    <span class="menu-title">View Products</span>
                </a>
            </li>
        @endif

        @if(auth()->user()->hasRole(['super_admin', 'admin']))
            <!-- Reports & Analytics -->
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#reports-menu" aria-expanded="false" aria-controls="reports-menu">
                    <i class="typcn typcn-chart-pie menu-icon"></i>
                    <span class="menu-title">Reports</span>
                    <i class="typcn typcn-chevron-right menu-arrow"></i>
                </a>
                <div class="collapse" id="reports-menu">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"><a class="nav-link" href="{{ route('barcodes.generate') }}">Barcode Generator</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('blog.listing') }}">Blog Management</a></li>
                    </ul>
                </div>
            </li>
        @endif

        <!-- Profile Settings - Available to all -->
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#profile-menu" aria-expanded="false" aria-controls="profile-menu">
                <i class="typcn typcn-cog-outline menu-icon"></i>
                <span class="menu-title">Profile</span>
                <i class="typcn typcn-chevron-right menu-arrow"></i>
            </a>
            <div class="collapse" id="profile-menu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link" href="{{ route('user.profile') }}">My Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('user.settings') }}">Settings</a></li>
                </ul>
            </div>
        </li>

        @if(auth()->user()->hasRole(['super_admin', 'admin']))
            <!-- Documentation -->
            <li class="nav-item">
                <a class="nav-link" href="{{ asset('template/pages/documentation/documentation.html') }}">
                    <i class="typcn typcn-document-text menu-icon"></i>
                    <span class="menu-title">Documentation</span>
                </a>
            </li>
        @endif
    </ul>
    
    <ul class="sidebar-legend">
        <li>
            <p class="sidebar-menu-title">Quick Links</p>
        </li>
        @if(auth()->user()->hasRole('seller'))
            <li class="nav-item"><a href="{{ route('pos') }}" class="nav-link">#POS</a></li>
            <li class="nav-item"><a href="{{ route('customers.create') }}" class="nav-link">#Add Customer</a></li>
        @endif
        @if(auth()->user()->hasRole(['super_admin', 'admin']))
            <li class="nav-item"><a href="{{ route('products.create') }}" class="nav-link">#Add Product</a></li>
            <li class="nav-item"><a href="{{ route('admin.sales.index') }}" class="nav-link">#Sales</a></li>
        @endif
    </ul>
</nav>