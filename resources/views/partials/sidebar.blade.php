<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="/dashboard" class="brand-link">
        <span class="brand-text font-weight-light">ERP System</span>
    </a>

    <div class="sidebar">
        <nav class="mt-2 text-sm">
            <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu">

                {{-- OVERVIEW --}}
                <li class="nav-header">OVERVIEW</li>
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('approvals.index') }}"
                        class="nav-link {{ request()->routeIs('approvals.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shield-alt text-warning"></i>
                        <p>Approvals</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('reports.profit-loss') }}"
                        class="nav-link {{ request()->is('reports/*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>
                            Business Analytics
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('reports.profit-loss') }}"
                                class="nav-link {{ request()->routeIs('reports.profit-loss') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon text-success"></i>
                                <p>Profit & Loss</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('reports.balance-sheet') }}"
                                class="nav-link {{ request()->routeIs('reports.balance-sheet') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon text-info"></i>
                                <p>Balance Sheet</p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- SALES & CUSTOMER --}}
                <li class="nav-header">SALES & CUSTOMER</li>
                <li
                    class="nav-item has-treeview {{ request()->is('sales*') || request()->is('customers*') || request()->is('marketplaces*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->is('sales*') || request()->is('customers*') || request()->is('marketplaces*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shopping-bag"></i>
                        <p>
                            Sales Management
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item"><a href="{{ route('customers.index') }}"
                                class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon"></i>
                                <p>Customers</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('sales-orders.index') }}"
                                class="nav-link {{ request()->routeIs('sales-orders.*') ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon"></i>
                                <p>Sales Orders (SO)</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('delivery-orders.index') }}"
                                class="nav-link {{ request()->routeIs('delivery-orders.*') ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon"></i>
                                <p>Delivery Orders (DO)</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('sales-invoices.index') }}"
                                class="nav-link {{ request()->routeIs('sales-invoices.*') ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon"></i>
                                <p>Sales Invoices (AR)</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('marketplaces.index') }}"
                                class="nav-link {{ request()->routeIs('marketplaces.*') ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon text-warning"></i>
                                <p>Marketplaces</p>
                            </a></li>
                    </ul>
                </li>

                {{-- PROCUREMENT --}}
                <li class="nav-header">PROCUREMENT</li>
                <li
                    class="nav-item has-treeview {{ request()->is('procurement*') || request()->is('suppliers*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->is('procurement*') || request()->is('suppliers*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>
                            Supply Chain
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item"><a href="{{ route('suppliers.index') }}"
                                class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon"></i>
                                <p>Suppliers</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('purchase-orders.index') }}"
                                class="nav-link {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon"></i>
                                <p>Purchase Orders (PO)</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('goods-receipts.index') }}"
                                class="nav-link {{ request()->routeIs('goods-receipts.*') ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon"></i>
                                <p>Goods Receipts (GRN)</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('purchase-invoices.index') }}"
                                class="nav-link {{ request()->routeIs('purchase-invoices.*') ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon"></i>
                                <p>Purchase Invoices (AP)</p>
                            </a></li>
                    </ul>
                </li>

                {{-- INVENTORY --}}
                <li class="nav-header">INVENTORY</li>
                <li
                    class="nav-item has-treeview {{ request()->is('products*') || request()->is('inventory*') || request()->is('warehouses*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->is('products*') || request()->is('inventory*') || request()->is('warehouses*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-boxes"></i>
                        <p>
                            Warehouse Center
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item"><a href="{{ route('products.index') }}"
                                class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon"></i>
                                <p>Products Registry</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('product-categories.index') }}"
                                class="nav-link {{ request()->routeIs('product-categories.*') ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon"></i>
                                <p>Product Categories</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('warehouses.index') }}"
                                class="nav-link {{ request()->routeIs('warehouses.*') ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon"></i>
                                <p>Warehouses</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('stock-cards.index') }}"
                                class="nav-link {{ request()->routeIs('stock-cards.*') ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon text-info"></i>
                                <p>Stock Balances / Cards</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('stock-transfers.index') }}"
                                class="nav-link {{ request()->routeIs('stock-transfers.*') ? 'active' : '' }}"><i
                                    class="far fa-toggle-on nav-icon"></i>
                                <p>Stock Transfers</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('stock-adjustments.index') }}"
                                class="nav-link {{ request()->routeIs('stock-adjustments.*') ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon text-danger"></i>
                                <p>Stock Adjustments</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('units.index') }}"
                                class="nav-link {{ request()->routeIs('units.*') ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon"></i>
                                <p>Units of Measure</p>
                            </a></li>
                    </ul>
                </li>

                {{-- FINANCE --}}
                <li class="nav-header">FINANCE & ASSETS</li>
                <li
                    class="nav-item has-treeview {{ request()->is('finance*') || request()->is('accounts*') || request()->is('journals*') || request()->is('fiscal*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->is('finance*') || request()->is('accounts*') || request()->is('journals*') || request()->is('fiscal*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-university"></i>
                        <p>
                            Accounting Core
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item"><a href="{{ route('bank-accounts.index') }}"
                                class="nav-link {{ request()->routeIs('bank-accounts.*') ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon"></i>
                                <p>Bank Accounts</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('bank-transactions.index') }}"
                                class="nav-link {{ request()->routeIs('bank-transactions.*') ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon"></i>
                                <p>Bank Transactions</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('assets.index') }}"
                                class="nav-link {{ request()->routeIs('assets.*') ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon"></i>
                                <p>Fixed Assets</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('asset-categories.index') }}"
                                class="nav-link {{ request()->routeIs('asset-categories.*') ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon"></i>
                                <p>Asset Settings</p>
                            </a></li>
                        <li class="nav-header">GENERAL LEDGER</li>
                        <li class="nav-item"><a href="{{ route('accounts.index') }}"
                                class="nav-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}"><i
                                    class="far fa-dot-circle nav-icon"></i>
                                <p>Chart of Accounts</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('journals.index') }}"
                                class="nav-link {{ request()->routeIs('journals.*') ? 'active' : '' }}"><i
                                    class="far fa-dot-circle nav-icon"></i>
                                <p>Journal Entries</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('fiscal-years.index') }}"
                                class="nav-link {{ request()->routeIs('fiscal-years.*') ? 'active' : '' }}"><i
                                    class="far fa-dot-circle nav-icon"></i>
                                <p>Fiscal Years</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('tax-rates.index') }}"
                                class="nav-link {{ request()->routeIs('tax-rates.*') ? 'active' : '' }}"><i
                                    class="far fa-dot-circle nav-icon"></i>
                                <p>Tax Settings</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('currencies.index') }}"
                                class="nav-link {{ request()->routeIs('currencies.*') ? 'active' : '' }}"><i
                                    class="far fa-dot-circle nav-icon"></i>
                                <p>Currencies</p>
                            </a></li>
                    </ul>
                </li>

                {{-- HR --}}
                <li class="nav-header">HUMAN RESOURCES</li>
                <li
                    class="nav-item has-treeview {{ request()->is('hr*') || request()->is('employees*') || request()->is('departments*') || request()->is('job-positions*') || request()->is('attendances*') || request()->is('leave*') || request()->is('payrolls*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->is('hr*') || request()->is('employees*') || request()->is('departments*') || request()->is('job-positions*') || request()->is('attendances*') || request()->is('leave*') || request()->is('payrolls*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-friends"></i>
                        <p>
                            Workforce
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item"><a href="{{ route('employees.index') }}"
                                class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon text-primary"></i>
                                <p>Employees</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('attendances.index') }}"
                                class="nav-link {{ request()->routeIs('attendances.*') ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon text-warning"></i>
                                <p>Attendance</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('payrolls.index') }}"
                                class="nav-link {{ request()->routeIs('payrolls.*') ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon text-success"></i>
                                <p>Payroll Process</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('leave-requests.index') }}"
                                class="nav-link {{ request()->routeIs('leave-requests.*') ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon text-info"></i>
                                <p>Leave Requests</p>
                            </a></li>
                        <li class="nav-header">HR MASTER</li>
                        <li class="nav-item"><a href="{{ route('departments.index') }}"
                                class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}"><i
                                    class="far fa-dot-circle nav-icon"></i>
                                <p>Departments</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('job-positions.index') }}"
                                class="nav-link {{ request()->routeIs('job-positions.*') ? 'active' : '' }}"><i
                                    class="far fa-dot-circle nav-icon"></i>
                                <p>Job Positions</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('leave-types.index') }}"
                                class="nav-link {{ request()->routeIs('leave-types.*') ? 'active' : '' }}"><i
                                    class="far fa-dot-circle nav-icon"></i>
                                <p>Leave Types</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('payroll-components.index') }}"
                                class="nav-link {{ request()->routeIs('payroll-components.*') ? 'active' : '' }}"><i
                                    class="far fa-dot-circle nav-icon"></i>
                                <p>Payroll Components</p>
                            </a></li>
                    </ul>
                </li>

                {{-- SYSTEM --}}
                <li class="nav-header">SYSTEM & CONTROL</li>
                <li
                    class="nav-item has-treeview {{ request()->is('admin*') || request()->is('users*') || request()->is('roles*') || request()->is('audit*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->is('admin*') || request()->is('users*') || request()->is('roles*') || request()->is('audit*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tools"></i>
                        <p>
                            Administration
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item"><a href="{{ route('users.index') }}"
                                class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon"></i>
                                <p>User Accounts</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('roles.index') }}"
                                class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon"></i>
                                <p>Roles & Permissions</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('audit-logs.index') }}"
                                class="nav-link {{ request()->routeIs('audit-logs.*') ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon text-danger"></i>
                                <p>System Logs (Audit)</p>
                            </a></li>
                    </ul>
                </li>

            </ul>
        </nav>
    </div>
</aside>
