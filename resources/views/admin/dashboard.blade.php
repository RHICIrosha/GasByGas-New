@extends('layouts.Admin')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Dashboard</h2>
        <p class="text-muted">Welcome back, <span id="admin-greeting">Administrator</span></p>
    </div>
</div>

<!-- Summary Cards Row -->
<div class="row dashboard-summary mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="card summary-card bg-white">
            <div class="card-body">
                <i class="fas fa-users icon text-primary"></i>
                <div class="label">Total Users</div>
                <div class="value">2,534</div>
                <div class="trend text-success">
                    <i class="fas fa-arrow-up"></i> 12% from last month
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card summary-card bg-white">
            <div class="card-body">
                <i class="fas fa-shopping-cart icon text-success"></i>
                <div class="label">Active Orders</div>
                <div class="value">156</div>
                <div class="trend text-primary">
                    <i class="fas fa-sync"></i> Live Updating
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card summary-card bg-white">
            <div class="card-body">
                <i class="fas fa-box icon text-warning"></i>
                <div class="label">Stock Level</div>
                <div class="value">1,245</div>
                <div class="trend text-warning">
                    <i class="fas fa-exclamation-triangle"></i> Low Stock Alert
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card summary-card bg-white">
            <div class="card-body">
                <i class="fas fa-dollar-sign icon text-info"></i>
                <div class="label">Monthly Revenue</div>
                <div class="value">$45,678</div>
                <div class="trend text-success">
                    <i class="fas fa-arrow-up"></i> 8% from last month
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Order Analytics</h5>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-secondary active">Weekly</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary">Monthly</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary">Yearly</button>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="ordersChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Distribution by Region</h5>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="regionChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders Table -->
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Recent Orders</h5>
        <div>
            <div class="input-group input-group-sm">
                <input type="text" class="form-control" placeholder="Search orders...">
                <button class="btn btn-outline-secondary" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>#ORD-2024-001</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="/img/avatar-placeholder.jpg" class="rounded-circle me-2" width="32" height="32">
                                <div>
                                    <div class="fw-bold">John Doe</div>
                                    <div class="small text-muted">john.doe@example.com</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-info">Residential</span></td>
                        <td>Mar 15, 2024</td>
                        <td><span class="badge bg-success">Delivered</span></td>
                        <td>$125.00</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i>View</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-print me-2"></i>Print</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-trash me-2"></i>Delete</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>#ORD-2024-002</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="/img/avatar-placeholder.jpg" class="rounded-circle me-2" width="32" height="32">
                                <div>
                                    <div class="fw-bold">Jane Smith</div>
                                    <div class="small text-muted">jane.smith@example.com</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-warning text-dark">Business</span></td>
                        <td>Mar 14, 2024</td>
                        <td><span class="badge bg-primary">Processing</span></td>
                        <td>$350.00</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i>View</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-print me-2"></i>Print</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-trash me-2"></i>Delete</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>#ORD-2024-003</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="/img/avatar-placeholder.jpg" class="rounded-circle me-2" width="32" height="32">
                                <div>
                                    <div class="fw-bold">Robert Johnson</div>
                                    <div class="small text-muted">robert@example.com</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-danger">Industrial</span></td>
                        <td>Mar 13, 2024</td>
                        <td><span class="badge bg-warning text-dark">Pending</span></td>
                        <td>$1,250.00</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i>View</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-print me-2"></i>Print</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-trash me-2"></i>Delete</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>#ORD-2024-004</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="/img/avatar-placeholder.jpg" class="rounded-circle me-2" width="32" height="32">
                                <div>
                                    <div class="fw-bold">Sarah Wilson</div>
                                    <div class="small text-muted">sarah@example.com</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-info">Residential</span></td>
                        <td>Mar 12, 2024</td>
                        <td><span class="badge bg-secondary">Cancelled</span></td>
                        <td>$85.00</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i>View</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-print me-2"></i>Print</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-trash me-2"></i>Delete</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <div>Showing <strong>1-4</strong> of <strong>125</strong> orders</div>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a>

@endsection

