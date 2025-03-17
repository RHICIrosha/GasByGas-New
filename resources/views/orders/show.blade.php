@extends('layouts.customer')

@section('content')
<div class="main-content" id="mainContent">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold">Order Details</h2>
            <p class="lead">Order #{{ $order->request_number }}</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('customer.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="dashboard-card border-0 shadow">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">Order Information</h4>
                    <span class="badge {{
                        $order->status == 'Pending' ? 'bg-warning text-dark' :
                        ($order->status == 'Confirmed' ? 'bg-primary' :
                        ($order->status == 'Cancelled' ? 'bg-success' : 'bg-danger'
                        ($order->status == 'Completed' ? 'bg-success' : 'bg-danger')))
                    }} fs-6">{{ $order->status }}</span>
                </div>

                <div class="row g-4">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">Order Details</h6>
                            <div class="border-start border-warning ps-3">
                                <p class="mb-2"><strong>Order Number:</strong> {{ $order->request_number }}</p>
                                <p class="mb-2"><strong>Date Placed:</strong> {{ $order->created_at->format('M d, Y h:i A') }}</p>
                                <p class="mb-2"><strong>Status:</strong> {{ $order->status }}</p>
                                @if($order->token)
                                <p class="mb-2"><strong>Token Number:</strong> {{ $order->token->token_number }}</p>
                                <p class="mb-0"><strong>Valid Until:</strong> {{ $order->token->valid_until->format('M d, Y') }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="text-muted mb-2">Gas Information</h6>
                            <div class="border-start border-warning ps-3">
                                <p class="mb-2"><strong>Gas Type:</strong> {{ $order->gasType->name }}</p>
                                <p class="mb-2"><strong>Category:</strong> {{ ucfirst($order->gasType->category) }}</p>
                                <p class="mb-2"><strong>Weight:</strong> {{ $order->gasType->weight }} kg</p>
                                <p class="mb-2"><strong>Quantity:</strong> {{ $order->quantity ?? 1 }}</p>
                                <p class="mb-0"><strong>Total Amount:</strong> Rs. {{ number_format($order->amount, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">Outlet Information</h6>
                            <div class="border-start border-warning ps-3">
                                <p class="mb-2"><strong>Outlet:</strong> {{ $order->outlet->name }}</p>
                                <p class="mb-2"><strong>Address:</strong> {{ $order->outlet->address }}</p>
                                <p class="mb-0"><strong>Contact:</strong> {{ $order->outlet->contact_number }}</p>
                            </div>
                        </div>

                        @if($order->notes)
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">Additional Notes</h6>
                            <div class="border-start border-warning ps-3">
                                <p class="mb-0">{{ $order->notes }}</p>
                            </div>
                        </div>
                        @endif

                        <div class="mt-4">
                            <h6 class="text-muted mb-2">Actions</h6>
                            <div class="d-grid gap-2">
                                @if(in_array($order->status, ['Pending', 'Confirmed']))
                                <a href="{{ route('orders.track', $order->id) }}" class="btn btn-warning">
                                    <i class="fas fa-map-marker-alt me-2"></i>Track Order
                                </a>
                                @endif
                                @if($order->status == 'Pending')
                                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelOrderModal">
                                    <i class="fas fa-times-circle me-2"></i>Cancel Order
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if(in_array($order->status, ['Pending', 'Confirmed']))
                <div class="alert alert-info mt-4 mb-0">
                    <div class="d-flex">
                        <div class="me-3">
                            <i class="fas fa-info-circle fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="alert-heading">Next Steps</h5>
                            <p class="mb-0">
                                @if($order->status == 'Pending')
                                    Your order is currently pending. You will be notified once it's confirmed by the outlet.
                                @elseif($order->status == 'Confirmed')
                                    Your order is confirmed! Please visit {{ $order->outlet->name }} with your empty cylinder and the payment before {{ $order->token->valid_until->format('M d, Y') }}.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Cancel Order Modal -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel this order? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <form action="{{ route('orders.cancel', $order->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-danger">Cancel Order</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .dashboard-card {
        border-radius: 15px;
        padding: 25px;
    }

    .badge {
        padding: 8px 15px;
        border-radius: 30px;
    }
</style>
@endsection
