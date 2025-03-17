<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{% block title %}GasByGas Admin{% endblock %}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/admin-styles.css">
    <style>
        /* GasByGas Admin Styles */

:root {
    --primary-color: #f7d44a;
    --primary-dark: #e6c32d;
    --secondary-color: #f8a427;
    --secondary-dark: #e79316;
    --success-color: #28a745;
    --info-color: #17a2b8;
    --warning-color: #ffc107;
    --danger-color: #dc3545;
    --light-color: #f8f9fa;
    --dark-color: #343a40;
    --border-radius: 0.375rem;
    --box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    --transition-speed: 0.3s;
}

body {
    background-color: #f8f9fa;
    font-family: 'Arial', sans-serif;
    color: #333;
}

/* Navbar Styles */
.navbar {
    background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    padding: 0.75rem 1rem;
}

.navbar-brand {
    font-weight: bold;
    display: flex;
    align-items: center;
}

.navbar-brand img {
    margin-right: 10px;
}

/* Sidebar Styles */
.sidebar {
    background: #fff;
    min-height: calc(100vh - 56px);
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    transition: all var(--transition-speed);
    z-index: 100;
}

@media (max-width: 768px) {
    .sidebar {
        position: fixed;
        left: -100%;
        top: 56px;
        width: 250px;
        z-index: 1000;
        transition: left var(--transition-speed);
    }

    .sidebar.show {
        left: 0;
    }

    .content-area {
        width: 100%;
    }
}

.nav-link {
    color: #333;
    padding: 0.8rem 1rem;
    border-radius: 0;
    transition: all var(--transition-speed);
    display: flex;
    align-items: center;
    border-left: 3px solid transparent;
}

.nav-link:hover {
    background-color: #fff3cd;
    color: var(--secondary-dark);
    border-left: 3px solid var(--secondary-color);
}

.nav-link.active {
    background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
    color: #fff;
    border-left: 3px solid var(--secondary-dark);
}

.nav-link i {
    margin-right: 10px;
    width: 20px;
    text-align: center;
}

/* Button Styles */
.btn-custom {
    background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
    border: none;
    color: #fff;
    transition: all var(--transition-speed);
}

.btn-custom:hover, .btn-custom:focus {
    opacity: 0.9;
    color: #fff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.btn-outline-custom {
    border: 1px solid var(--primary-color);
    color: var(--secondary-color);
    background: transparent;
    transition: all var(--transition-speed);
}

.btn-outline-custom:hover, .btn-outline-custom:focus {
    background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
    color: #fff;
}

/* Card Styles */
.card {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    margin-bottom: 1.5rem;
    transition: all var(--transition-speed);
}

.card:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.card-header {
    background-color: #fff;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    font-weight: 600;
}

.stats-card {
    position: relative;
    overflow: hidden;
}

.stats-card .card-body {
    position: relative;
    z-index: 2;
}

.stats-card .card-icon {
    font-size: 4rem;
    position: absolute;
    bottom: -1rem;
    right: -1rem;
    opacity: 0.2;
    z-index: 1;
}

/* Table Styles */
.table thead th {
    background-color: #fff3cd;
    border-top: none;
    font-weight: 600;
    color: #333;
}

.table-hover tbody tr:hover {
    background-color: rgba(247, 212, 74, 0.05);
}

/* Form Styles */
.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(247, 212, 74, 0.25);
}

.form-label {
    font-weight: 500;
}

/* Status Indicators */
.status-indicator {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 5px;
}

.status-active {
    background-color: var(--success-color);
}

.status-pending {
    background-color: var(--warning-color);
}

.status-inactive {
    background-color: var(--danger-color);
}

/* Animations */
.fade-in {
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Dashboard specific styles */
.dashboard-summary {
    margin-bottom: 2rem;
}

.summary-card {
    position: relative;
    overflow: hidden;
    border-radius: var(--border-radius);
    height: 100%;
}

.summary-card .icon {
    position: absolute;
    right: 1rem;
    top: 1rem;
    font-size: 2rem;
    opacity: 0.2;
}

.summary-card .value {
    font-size: 2rem;
    font-weight: bold;
}

.summary-card .label {
    color: #6c757d;
    font-size: 0.875rem;
}

.summary-card .trend {
    margin-top: 0.5rem;
    font-size: 0.875rem;
}

/* Responsive adjustments */
@media (max-width: 576px) {
    .dashboard-summary .col-sm-6 {
        margin-bottom: 1rem;
    }

    .navbar .navbar-brand span {
        display: none;
    }
}

/* Print styles */
@media print {
    .sidebar, .navbar {
        display: none;
    }

    .content-area {
        width: 100%;
        margin: 0;
        padding: 0;
    }

    .card {
        box-shadow: none;
        border: 1px solid #ddd;
    }
}
@yield('styles');
    </style>

</head>
<body>
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/admin/dashboard">
                <img src="/img/logo.png" alt="Logo" width="40" height="40" class="d-inline-block align-text-top">
                GasByGas Admin
            </a>
            <div class="d-flex align-items-center">
                <div class="dropdown me-3">
                    {{-- <a class="dropdown-toggle text-white text-decoration-none" href="#" role="button" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell"></i>
                        <span class="badge bg-danger rounded-pill">3</span>
                    </a> --}}

                </div>
                <div class="dropdown">
                    <a class="dropdown-toggle text-white text-decoration-none" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <span id="admin-name">{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="/admin/profile"><i class="fas fa-user me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="/admin/settings"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="#" id="logout-btn">
                                <form action="{{ route('logout') }}" method="POST">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </form>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Navigation -->
            <div class="col-md-2 sidebar pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {% if active_page == 'dashboard' %}active{% endif %}" href="/admin/dashboard">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {% if active_page == 'distribution' %}active{% endif %}" href="/admin/distribution">
                            <i class="fas fa-truck-loading me-2"></i>Distribution
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {% if active_page == 'outlets' %}active{% endif %}" href="/admin/outlets">
                            <i class="fas fa-store me-2"></i>Outlets
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {% if active_page == 'inventory' %}active{% endif %}" href="/admin/inventory">
                            <i class="fas fa-boxes me-2"></i>Inventory
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {% if active_page == 'orders' %}active{% endif %}" href="/admin/orders">
                            <i class="fas fa-shopping-cart me-2"></i>Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {% if active_page == 'users' %}active{% endif %}" href="/admin/users">
                            <i class="fas fa-users-cog me-2"></i>User Management
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {% if active_page == 'reports' %}active{% endif %}" href="/admin/reports">
                            <i class="fas fa-chart-line me-2"></i>Reports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {% if active_page == 'settings' %}active{% endif %}" href="/admin/settings">
                            <i class="fas fa-cogs me-2"></i>Settings
                        </a>
                    </li>
                </ul>

                <div class="mt-5 p-3">
                    <div class="card bg-light border-0">
                        <div class="card-body p-3">
                            <h6 class="text-muted mb-3">System Status</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Server</span>
                                <span class="badge bg-success">Online</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Database</span>
                                <span class="badge bg-success">Connected</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Last Backup</span>
                                <span class="text-muted small">Today, 03:45 AM</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="col-md-10 p-4 content-area">
                @yield('content')

            </div>
        </div>
    </div>
    @yield('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
   

    <script>
        // Common admin layout JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // Highlight current active menu item
            const currentLocation = window.location.pathname;
            const menuItems = document.querySelectorAll('.sidebar .nav-link');

            menuItems.forEach(item => {
                if (currentLocation.includes(item.getAttribute('href'))) {
                    item.classList.add('active');
                }
            });

            // Logout functionality
            const logoutBtn = document.getElementById('logout-btn');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Show confirmation dialog
                    if (confirm('Are you sure you want to logout?')) {
                        // Perform logout action (can be replaced with actual logout logic)
                        window.location.href = '/admin/logout';
                    }
                });
            }

            // Fetch admin user info (can be replaced with actual API call)
            fetchAdminUserInfo();
        });

        function fetchAdminUserInfo() {
            // This would normally be an API call to get the current admin user info
            // For now, we'll just simulate it
            const adminName = document.getElementById({{ Auth::user()->name }});
            if (adminName) {
                // Simulate API response
                setTimeout(() => {
                    adminName.textContent = {{ Auth::user()->name }};
                }, 500);
            }
        }
    </script>
</body>
</html>
