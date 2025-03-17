@extends('layouts.customer')

@section('content')
<!-- Main Content -->
<div class="main-content" id="mainContent">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold">Order History</h2>
            <p class="lead">Track and manage your gas orders</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('orders.create') }}" class="btn btn-warning">
                <i class="fas fa-plus-circle me-2"></i>Place New Order
            </a>
        </div>
    </div>

    <!-- Order Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="dashboard-card d-flex align-items-center">
                <div class="card-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div>
                    <h5 class="mb-0">{{ $orders->total() }}</h5>
                    <p class="text-muted mb-0">Total Orders</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card d-flex align-items-center">
                <div class="card-icon" style="background: #ffc107;">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div>
                    <h5 class="mb-0">{{ $pendingCount ?? 0 }}</h5>
                    <p class="text-muted mb-0">Pending Orders</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card d-flex align-items-center">
                <div class="card-icon" style="background: #28a745;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <h5 class="mb-0">{{ $completedCount ?? 0 }}</h5>
                    <p class="text-muted mb-0">Completed Orders</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card d-flex align-items-center">
                <div class="card-icon" style="background: #17a2b8;">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div>
                    <h5 class="mb-0">{{ $activeTokensCount ?? 0 }}</h5>
                    <p class="text-muted mb-0">Active Tokens</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="dashboard-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Your Orders</h4>

            <!-- Simple Filter -->
            <div class="btn-group">
                <a href="{{ route('orders.history') }}" class="btn {{ !request('status') ? 'btn-warning' : 'btn-outline-secondary' }}">All</a>
                <a href="{{ route('orders.history', ['status' => 'Pending']) }}" class="btn {{ request('status') == 'Pending' ? 'btn-warning' : 'btn-outline-secondary' }}">Pending</a>
                <a href="{{ route('orders.history', ['status' => 'Confirmed']) }}" class="btn {{ request('status') == 'Confirmed' ? 'btn-warning' : 'btn-outline-secondary' }}">Confirmed</a>
                <a href="{{ route('orders.history', ['status' => 'Completed']) }}" class="btn {{ request('status') == 'Completed' ? 'btn-warning' : 'btn-outline-secondary' }}">Completed</a>
            </div>
        </div>

        @if($orders->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-receipt text-muted" style="font-size: 3rem;"></i>
                <h4 class="mt-3">No Orders Found</h4>
                <p class="text-muted">You haven't placed any orders yet.</p>
                <a href="{{ route('orders.create') }}" class="btn btn-warning mt-2">
                    <i class="fas fa-plus-circle me-2"></i>Place Your First Order
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover order-history-table">
                    <thead>
                        <tr>
                            <th>Request #</th>
                            <th>Date</th>
                            <th>Gas Type</th>
                            <th>Quantity</th>
                            <th>Outlet</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>{{ $order->request_number }}</td>
                                <td>{{ $order->created_at->format('M d, Y') }}</td>
                                <td>{{ $order->gasType->name }}</td>
                                <td>{{ $order->quantity }}</td>
                                <td>{{ $order->outlet->name }}</td>
                                <td>Rs. {{ number_format($order->amount, 2) }}</td>
                                <td>
                                    <span class="status-indicator {{ strtolower($order->status) }}"></span>
                                    {{ $order->status }}
                                </td>
                                <td>
                                    <a href="{{ route('orders.track', $order->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-map-marker-alt"></i> Track
                                    </a>
                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-eye"></i> Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    <!-- Order Status Explanation -->
    <div class="dashboard-card">
        <h4 class="mb-4">Order Status Guide</h4>
        <div class="row">
            <div class="col-md-6">
                <div class="d-flex align-items-center mb-3">
                    <span class="status-indicator pending me-2"></span>
                    <div>
                        <strong>Pending</strong>
                        <p class="text-muted mb-0">Your order has been received and is awaiting confirmation.</p>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <span class="status-indicator confirmed me-2"></span>
                    <div>
                        <strong>Confirmed</strong>
                        <p class="text-muted mb-0">Your order is confirmed and ready for pickup.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center mb-3">
                    <span class="status-indicator completed me-2"></span>
                    <div>
                        <strong>Completed</strong>
                        <p class="text-muted mb-0">You have successfully received your gas cylinder.</p>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <span class="status-indicator cancelled me-2"></span>
                    <div>
                        <strong>Cancelled</strong>
                        <p class="text-muted mb-0">The order was cancelled.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .status-indicator {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 5px;
    }

    .status-indicator.pending {
        background-color: #ffc107;
    }

    .status-indicator.confirmed {
        background-color: #17a2b8;
    }

    .status-indicator.completed {
        background-color: #28a745;
    }

    .status-indicator.cancelled {
        background-color: #dc3545;
    }
</style>
@endsection
