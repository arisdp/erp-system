<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="/dashboard" class="brand-link">
        <span class="brand-text font-weight-light">ERP System</span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">

                {{-- Dashboard --}}
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                {{-- Group: ORGANIZATION --}}
                <li class="nav-header">ORGANIZATION</li>
                <li class="nav-item has-treeview {{ request()->is('master/*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('master/*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-building"></i>
                        <p>
                            Master Data
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('companies.index') }}"
                                class="nav-link {{ request()->routeIs('companies.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Company</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('branches.index') }}"
                                class="nav-link {{ request()->routeIs('branches.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Branch</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('departments.index') }}"
                                class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Department</p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Group: ACCESS CONTROL --}}
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-user-shield"></i>
                        <p>
                            Access Control
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('users.index') }}"
                                class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Users</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('roles.index') }}"
                                class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Roles & Permissions</p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Group: ACCOUNTING --}}
                <li class="nav-header">FINANCE & ACCOUNTING</li>
                <li class="nav-item">
                    <a href="{{ route('accounts.index') }}"
                        class="nav-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-sitemap"></i>
                        <p>Chart of Accounts</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('account-groups.index') }}"
                        class="nav-link {{ request()->routeIs('account-groups.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-layer-group"></i>
                        <p>Account Groups</p>
                    </a>
                </li>
                <li class="nav-item border-bottom pb-2 mb-2" style="border-color: #4b545c !important;">
                    <a href="{{ route('fiscal-years.index') }}"
                        class="nav-link {{ request()->routeIs('fiscal-years.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>Fiscal Years</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('journals.index') }}"
                        class="nav-link {{ request()->routeIs('journals.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-book"></i>
                        <p>Journal Entries</p>
                    </a>
                </li>

                <li class="nav-header">INVENTORY & LOGISTICS</li>
                <li class="nav-item">
                    <a href="{{ route('products.index') }}"
                        class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box"></i>
                        <p>Products</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('product-categories.index') }}"
                        class="nav-link {{ request()->routeIs('product-categories.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tags"></i>
                        <p>Categories</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('units.index') }}"
                        class="nav-link {{ request()->routeIs('units.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-balance-scale"></i>
                        <p>Units of Measure</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('warehouses.index') }}"
                        class="nav-link {{ request()->routeIs('warehouses.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-warehouse"></i>
                        <p>Warehouses</p>
                    </a>
                </li>

                <li class="nav-header">STAKEHOLDERS</li>
                <li class="nav-item">
                    <a href="{{ route('customers.index') }}"
                        class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Customers</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('suppliers.index') }}"
                        class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-truck"></i>
                        <p>Suppliers</p>
                    </a>
                </li>

                <li class="nav-header">FINANCE MASTER</li>
                <li class="nav-item">
                    <a href="{{ route('tax-rates.index') }}"
                        class="nav-link {{ request()->routeIs('tax-rates.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-percentage"></i>
                        <p>Tax Rates</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('currencies.index') }}"
                        class="nav-link {{ request()->routeIs('currencies.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-coins"></i>
                        <p>Currencies</p>
                    </a>
                </li>

                {{-- Group: LOGISTICS --}}
                <li class="nav-header">LOGISTICS</li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-boxes"></i>
                        <p>Inventory Control</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>Purchasing</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-cash-register"></i>
                        <p>Sales</p>
                    </a>
                </li>

                {{-- Group: HUMAN RESOURCES --}}
                <li class="nav-header">HUMAN RESOURCES</li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Employee Management</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-money-check-alt"></i>
                        <p>Payroll</p>
                    </a>
                </li>

                {{-- Group: PROCUREMENT --}}
                <li class="nav-header">PROCUREMENT</li>
                <li class="nav-item">
                    <a href="{{ route('purchase-orders.index') }}"
                        class="nav-link {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>Purchase Orders</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('goods-receipts.index') }}"
                        class="nav-link {{ request()->routeIs('goods-receipts.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-truck-loading"></i>
                        <p>Goods Receipts (GRN)</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('purchase-invoices.index') }}"
                        class="nav-link {{ request()->routeIs('purchase-invoices.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>Purchase Invoices (AP)</p>
                    </a>
                </li>

                {{-- Group: SALES --}}
                <li class="nav-header">SALES</li>
                <li class="nav-item">
                    <a href="{{ route('sales-orders.index') }}"
                        class="nav-link {{ request()->routeIs('sales-orders.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>Sales Orders (SO)</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('delivery-orders.index') }}"
                        class="nav-link {{ request()->routeIs('delivery-orders.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-truck"></i>
                        <p>Delivery Orders (DO)</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('sales-invoices.index') }}"
                        class="nav-link {{ request()->routeIs('sales-invoices.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>Sales Invoices (AR)</p>
                    </a>
                </li>

                {{-- Group: REPORTS --}}
                <li class="nav-header">REPORTS</li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>Financial Reports</p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>
