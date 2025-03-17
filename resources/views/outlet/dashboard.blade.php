@extends('layouts.outlet')

@section('title', 'Outlet Dashboard - GasByGas')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="page-title">
                <i class="fas fa-store-alt me-2"></i> {{ $outlet->name }} Dashboard
            </h2>
            <p class="text-muted">Welcome, {{ Auth::user()->name }} | Today: {{ now()->format('d M, Y') }}</p>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="outlet-status {{ $outlet->is_accepting_orders ? 'status-open' : 'status-closed' }}">
                <span>Status: {{ $outlet->is_accepting_orders ? 'Open for Orders' : 'Closed' }}</span>
                {{-- <form action="{{ route('outlet.toggle-status') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm {{ $outlet->is_accepting_orders ? 'btn-danger' : 'btn-success' }} ms-2">
                        {{ $outlet->is_accepting_orders ? 'Close Outlet' : 'Open Outlet' }}
                    </button>
                </form> --}}
            </div>
        </div>
    </div>

    <!-- Stats Cards Row -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card bg-primary">
                <div class="stat-card-body">
                    <h5 class="stat-card-title">Pending Tokens</h5>
                    <p class="stat-card-value">{{ $pendingTokensCount }}</p>
                    <p class="stat-card-desc">Awaiting Pickup</p>
                </div>
                <div class="stat-card-icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-success">
                <div class="stat-card-body">
                    <h5 class="stat-card-title">Today's Pickups</h5>
                    <p class="stat-card-value">{{ $todayPickupsCount }}</p>
                    <p class="stat-card-desc">{{ now()->format('d M') }}</p>
                </div>
                <div class="stat-card-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-warning">
                <div class="stat-card-body">
                    <h5 class="stat-card-title">Current Stock</h5>
                    <p class="stat-card-value">{{ $totalStock }}</p>
                    <p class="stat-card-desc">Cylinders Available</p>
                </div>
                <div class="stat-card-icon">
                    <i class="fas fa-layer-group"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-info">
                <div class="stat-card-body">
                    <h5 class="stat-card-title">Empty Returns</h5>
                    <p class="stat-card-value">{{ $emptyReturnsCount }}</p>
                    <p class="stat-card-desc">This Week</p>
                </div>
                <div class="stat-card-icon">
                    <i class="fas fa-exchange-alt"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions Row -->
    <div class="row mb-4">
        <div class="col-12">
            {{-- <div class="action-buttons">
                <a href="{{ route('outlet.tokens.index') }}" class="btn btn-primary">
                    <i class="fas fa-ticket-alt me-2"></i> Manage Tokens
                </a>
                <a href="{{ route('outlet.inventory.index') }}" class="btn btn-warning">
                    <i class="fas fa-boxes me-2"></i> Update Inventory
                </a>
                <a href="{{ route('outlet.tokens.verify') }}" class="btn btn-success">
                    <i class="fas fa-qrcode me-2"></i> Scan & Verify
                </a>
                <a href="{{ route('outlet.customers.index') }}" class="btn btn-info">
                    <i class="fas fa-users me-2"></i> View Customers
                </a>
            </div> --}}
        </div>
    </div>

    <!-- Pending Tokens Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Pending Tokens</h5>
                    {{-- <a href="{{ route('outlet.tokens.index') }}" class="btn btn-sm btn-outline-primary">View All</a> --}}
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Token #</th>
                                    <th>Customer</th>
                                    <th>Gas Type</th>
                                    <th>Quantity</th>
                                    <th>Requested</th>
                                    <th>Expires</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingTokens as $token)
                                <tr>
                                    <td><span class="token-number">{{ $token->token_number }}</span></td>
                                    <td>{{ $token->user->name }}</td>
                                    <td>{{ $token->gasRequest->gasType->name }}</td>
                                    <td>{{ $token->gasRequest->quantity }}</td>
                                    <td>{{ $token->created_at->format('d M, Y') }}</td>
                                    <td>{{ $token->valid_until->format('d M, Y') }}</td>
                                    <td><span class="badge bg-warning">Pending</span></td>
                                    <td>
                                        {{-- <a href="{{ route('outlet.tokens.process', $token->id) }}" class="btn btn-sm btn-success">
                                            <i class="fas fa-check"></i>
                                        </a>
                                        <a href="{{ route('outlet.tokens.detail', $token->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a> --}}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-3">No pending tokens</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Status Section -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Inventory Status</h5>
                    {{-- <a href="{{ route('outlet.inventory.index') }}" class="btn btn-sm btn-outline-warning">Update</a> --}}
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Gas Type</th>
                                    <th>In Stock</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($inventory as $item)
                                <tr>
                                    <td>{{ $item->gasType->name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>
                                        @if($item->quantity > 20)
                                            <span class="badge bg-success">Good</span>
                                        @elseif($item->quantity > 5)
                                            <span class="badge bg-warning">Low</span>
                                        @else
                                            <span class="badge bg-danger">Critical</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Upcoming Deliveries</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Delivery #</th>
                                    <th>Expected Date</th>
                                    <th>Total Items</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @forelse($upcomingDeliveries as $delivery)
                                <tr>
                                    <td>{{ $delivery->delivery_number }}</td>
                                    <td>{{ $delivery->scheduled_date->format('d M, Y') }}</td>
                                    <td>{{ $delivery->total_quantity }}</td>
                                    <td><span class="badge bg-info">{{ $delivery->status }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3">No upcoming deliveries</td>
                                </tr>
                                @endforelse --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    :root {
        --primary-yellow: #FFC107;
        --secondary-yellow: #FFD54F;
        --dark-yellow: #FFA000;
        --light-yellow: #FFF3E0;
    }

    body {
        background: var(--light-yellow);
        font-family: 'Poppins', sans-serif;
    }

    .page-title {
        color: #2C3E50;
        border-bottom: 2px solid var(--primary-yellow);
        padding-bottom: 10px;
        margin-bottom: 10px;
    }

    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        overflow: hidden;
        margin-bottom: 20px;
    }

    .card-header {
        background-color: white;
        border-bottom: 1px solid #eee;
        padding: 15px 20px;
    }

    .outlet-status {
        padding: 8px 15px;
        border-radius: 50px;
        font-weight: bold;
        display: inline-block;
    }

    .status-open {
        background-color: rgba(46, 204, 113, 0.2);
        color: #27ae60;
    }

    .status-closed {
        background-color: rgba(231, 76, 60, 0.2);
        color: #e74c3c;
    }

    .stat-card {
        border-radius: 15px;
        padding: 20px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 100%;
        min-height: 140px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .stat-card-body {
        flex: 1;
    }

    .stat-card-value {
        font-size: 28px;
        font-weight: bold;
        margin: 5px 0;
    }

    .stat-card-desc {
        font-size: 14px;
        opacity: 0.8;
        margin: 0;
    }

    .stat-card-icon {
        font-size: 40px;
        opacity: 0.8;
    }

    .action-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 20px;
    }

    .action-buttons .btn {
        padding: 12px 20px;
        border-radius: 10px;
        font-weight: 600;
    }

    .token-number {
        font-weight: bold;
        font-family: monospace;
        padding: 4px 8px;
        background: #f8f9fa;
        border-radius: 4px;
    }

    @media (max-width: 768px) {
        .action-buttons {
            flex-direction: column;
        }

        .action-buttons .btn {
            width: 100%;
            margin-bottom: 10px;
        }
    }
</style>
@endsection
