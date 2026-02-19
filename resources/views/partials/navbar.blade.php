<nav class="main-header navbar navbar-expand navbar-white navbar-light">

    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#">
                <i class="fas fa-bars"></i>
            </a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

        <!-- User Dropdown -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                {{ auth()->user()->name }}
            </a>
            <div class="dropdown-menu dropdown-menu-right">

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="dropdown-item text-danger">
                        Logout
                    </button>
                </form>

            </div>
        </li>

    </ul>

</nav>