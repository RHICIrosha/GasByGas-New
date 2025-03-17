@extends('layouts.Business')

@section('content')
<!-- Main Content -->
<div class="main-content" id="mainContent">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold">Dashboard</h2>
            <p class="lead">Welcome back, {{ Auth::user()->name }}! Manage your gas orders and track deliveries.</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('orders.create') }}" class="btn btn-warning">
                <i class="fas fa-plus-circle me-2"></i>New Gas Order
            </a>
        </div>
    </div>

    <!-- Dashboard Stats -->
     <div class="row mb-4">
        <div class="col-md-3">
            <div class="dashboard-card d-flex align-items-center">
                <div class="card-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div>
                    <h5 class="mb-0">{{ $totalOrders }}</h5>
                    <p class="text-muted mb-0">Total Orders</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card d-flex align-items-center">
                <div class="card-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <div>
                    <h5 class="mb-0">{{ $pendingDeliveries }}</h5>
                    <p class="text-muted mb-0">Pending Delivery</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card d-flex align-items-center">
                <div class="card-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div>
                    <h5 class="mb-0">{{ $completedOrders }}</h5>
                    <p class="text-muted mb-0">Completed Orders</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card d-flex align-items-center">
                <div class="card-icon">
                    <i class="fas fa-thumbs-up"></i>
                </div>
                <div>
                    <h5 class="mb-0">{{ $activeTokens }}</h5>
                    <p class="text-muted mb-0">Active Tokens</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Gas Types Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-4">Available Gas Types</h4>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Residential Gas -->
        <div class="col-md-3">
            <div class="gas-type-card">
                <div class="gas-type-img" style="background-image: url('{{ asset('img/12.png') }}');"></div>
                <div class="gas-type-info">
                    <span class="gas-badge">Domestic</span>
                    <h5>Residential Gas</h5>
                    <p class="text-muted">Standard household cylinder for cooking purposes (12.5kg)</p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="fw-bold">Rs. 2,750.00</span>
                        <a href="{{ route('orders.create', ['gas_type_id' => 1]) }}" class="btn btn-warning btn-sm">Order Now</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="gas-type-card">
                <div class="gas-type-img" style="background-image: url('{{ asset('img/5.png') }}');"></div>
                <div class="gas-type-info">
                    <span class="gas-badge">Domestic</span>
                    <h5>Domestic Gas</h5>
                    <p class="text-muted">Small household cylinder for cooking or portable use (5kg).</p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="fw-bold">Rs. 1,100.00</span>
                        <a href="{{ route('orders.create', ['gas_type_id' => 2]) }}" class="btn btn-warning btn-sm">Order Now</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Commercial Gas -->
        <div class="col-md-3">
            <div class="gas-type-card">
                <div class="gas-type-img" style="background-image: url('{{ asset('img/37.png') }}');"></div>
                <div class="gas-type-info">
                    <span class="gas-badge">Commercial</span>
                    <h5>Commercial Gas</h5>
                    <p class="text-muted">Commercial grade cylinder for restaurants and small businesses (37.5kg)</p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="fw-bold">Rs. 7,800.00</span>
                        <a href="{{ route('orders.create', ['gas_type_id' => 3]) }}" class="btn btn-warning btn-sm">Order Now</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Industrial Gas -->
        <div class="col-md-3">
            <div class="gas-type-card">
                <div class="gas-type-img" style="background-image: url('{{ asset('img/45.png') }}');"></div>
                <div class="gas-type-info">
                    <span class="gas-badge">Industrial</span>
                    <h5>Industrial Gas</h5>
                    <p class="text-muted">Large cylinder for industrial applications (45kg)</p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="fw-bold">Rs. 9,500.00</span>
                        <a href="{{ route('orders.create', ['gas_type_id' => 4]) }}" class="btn btn-warning btn-sm">Order Now</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders --

    <!-- Nearest Outlets -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="dashboard-card">
                <h4 class="mb-4">Nearest Outlets</h4>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Outlet Name</th>
                                <th>Address</th>
                                <th>Contact</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Colombo Outlet</td>
                                <td>123 Galle Road, Colombo 03</td>
                                <td>0111234567</td>
                                <td><span class="badge bg-success">Available</span></td>
                                <td>
                                    <a href="{{ route('orders.create', ['outlet_id' => 1]) }}" class="btn btn-sm btn-warning">Order</a>
                                </td>
                            </tr>
                            <tr>
                                <td>Negombo Outlet</td>
                                <td>200 Beach Road, Negombo</td>
                                <td>0312233445</td>
                                <td><span class="badge bg-success">Available</span></td>
                                <td>
                                    <a href="{{ route('orders.create', ['outlet_id' => 2]) }}" class="btn btn-sm btn-warning">Order</a>
                                </td>
                            </tr>
                            <tr>
                                <td>Kandy Outlet</td>
                                <td>45 Dalada Veediya, Kandy</td>
                                <td>0812233445</td>
                                <td><span class="badge bg-danger">Stock Out</span></td>
                                <td>
                                    <button class="btn btn-sm btn-warning" disabled>Order</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <a href="{{ route('outlets.find') }}" class="btn btn-outline-warning">View All Outlets</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
