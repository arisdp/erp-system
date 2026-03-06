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
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-book"></i>
                        <p>Journal Entries</p>
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
