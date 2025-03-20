<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'GasByGas Outlet Manager')</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Base Styles -->
    <style>
        :root {
            --primary-yellow: #FFC107;
            --secondary-yellow: #FFD54F;
            --dark-yellow: #FFA000;
            --light-yellow: #FFF3E0;
            --sidebar-width: 250px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-yellow);
            margin: 0;
            padding: 0;
        }

        .wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        .sidebar {
            width: var(--sidebar-width);
            background-color: #2C3E50;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            z-index: 100;
            transition: all 0.3s;
        }

        .sidebar-header {
            padding: 20px;
            background: #1a2530;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .sidebar-logo {
            width: 40px;
            height: 40px;
            background: var(--primary-yellow);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2C3E50;
            font-weight: bold;
            font-size: 18px;
        }

        .sidebar-brand {
            font-weight: 700;
            font-size: 1.2rem;
            margin: 0;
        }

        .sidebar-user {
            padding: 15px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-user-name {
            font-weight: 600;
            margin-bottom: 3px;
        }

        .sidebar-user-email {
            font-size: 0.8rem;
            opacity: 0.7;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .sidebar-menu-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 5px 20px;
            opacity: 0.6;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        .sidebar-menu-item {
            padding: 8px 20px;
            display: flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
            margin-bottom: 3px;
        }

        .sidebar-menu-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .sidebar-menu-item.active, .sidebar-menu-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--primary-yellow);
        }

        .content {
            width: calc(100% - var(--sidebar-width));
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            background: var(--light-yellow);
            transition: all 0.3s;
        }

        .topbar {
            background: white;
            height: 60px;
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .menu-toggle {
            display: none;
            cursor: pointer;
            padding: 5px;
        }

        .user-dropdown {
            position: relative;
        }

        .user-dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--primary-yellow);
            color: #2C3E50;
            font-weight: 600;
        }

        .notification-badge {
            position: relative;
            display: inline-block;
            margin-right: 20px;
        }

        .notification-badge i {
            color: #6c757d;
            font-size: 18px;
        }

        .badge-count {
            position: absolute;
            top: -5px;
            right: -8px;
            background: var(--primary-yellow);
            color: #2C3E50;
            width: 16px;
            height: 16px;
            font-size: 10px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .dropdown-menu {
            margin-top: 10px !important;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border: none;
        }

        @media (max-width: 768px) {
            .sidebar {
                margin-left: -var(--sidebar-width);
            }

            .content {
                width: 100%;
                margin-left: 0;
            }

            .menu-toggle {
                display: block;
            }

            .sidebar.active {
                margin-left: 0;
            }

            .content.active {
                margin-left: var(--sidebar-width);
                width: calc(100% - var(--sidebar-width));
            }
        }
    </style>

    @yield('styles')
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">G</div>
                <h5 class="sidebar-brand">GasByGas</h5>
            </div>

            <div class="sidebar-user">
                <div class="sidebar-user-name">{{ Auth::user()->name }}</div>
                <div class="sidebar-user-email">{{ Auth::user()->email }}</div>
            </div>

            <div class="sidebar-menu">
                <div class="sidebar-menu-title">Main Navigation</div>

                <a href="{{ route('outlet.dashboard') }}" class="sidebar-menu-item {{ request()->routeIs('outlet.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="{{ route('outlet.order-requests.index') }}" class="sidebar-menu-item {{ request()->routeIs('outlet.orders.*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart"></i> Request Gas
                </a>
{{--
                <a href="{{ route('outlet.tokens.index') }}" class="sidebar-menu-item {{ request()->routeIs('outlet.tokens.*') ? 'active' : '' }}">
                    <i class="fas fa-ticket-alt"></i> Tokens
                </a>

                <a href="{{ route('outlet.inventory.index') }}" class="sidebar-menu-item {{ request()->routeIs('outlet.inventory.*') ? 'active' : '' }}">
                    <i class="fas fa-boxes"></i> Inventory
                </a>

                <a href="{{ route('outlet.customers.index') }}" class="sidebar-menu-item {{ request()->routeIs('outlet.customers.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Customers
                </a>

                <div class="sidebar-menu-title">Quick Actions</div>

                <a href="{{ route('outlet.tokens.verify') }}" class="sidebar-menu-item {{ request()->routeIs('outlet.tokens.verify') ? 'active' : '' }}">
                    <i class="fas fa-qrcode"></i> Verify Token
                </a>

                <div class="sidebar-menu-title">Account</div>

                <a href="#" class="sidebar-menu-item">
                    <i class="fas fa-user-circle"></i> Profile
                </a>

                <a href="{{ route('logout') }}" class="sidebar-menu-item"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a> --}}

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="content" id="content">
            <div class="topbar">
                <div class="menu-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </div>

                <div class="d-flex align-items-center">
                    <div class="notification-badge">
                        <i class="fas fa-bell"></i>
                        <span class="badge-count">3</span>
                    </div>

                    <div class="user-dropdown">
                        <div class="user-dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down ms-1"></i>
                        </div>

                        <ul class="dropdown-menu" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user-circle me-2"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form-2').submit();">
                                   <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </a>

                                <form id="logout-form-2" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <main>
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sidebar Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                    content.classList.toggle('active');
                });
            }
        });
    </script>

    @yield('scripts')
</body>
</html>
