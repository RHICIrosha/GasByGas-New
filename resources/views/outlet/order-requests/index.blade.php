@extends('layouts.outlet')

@section('title', 'Order Requests - GasByGas')

@section('content')
<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Stock Order Requests</h2>
        <a href="{{ route('outlet.order-requests.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>New Stock Request
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">Your Order Requests</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <form method="GET" action="{{ route('outlet.order-requests.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                            <option value="Partial" {{ request('status') == 'Partial' ? 'selected' : '' }}>Partially Approved</option>
                            <option value="Fulfilled" {{ request('status') == 'Fulfilled' ? 'selected' : '' }}>Fulfilled</option>
                            <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search request number" value="{{ request('search') }}">
                            <button type="submit" class="btn btn-outline-secondary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('outlet.order-requests.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-sync-alt me-1"></i> Reset
                        </a>
                    </div>
                </form>
            </div>

            @if($requests->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> You haven't created any stock order requests yet.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Request #</th>
                                <th>Date Requested</th>
                                <th>Total Items</th>
                                <th>Status</th>
                                <th>Delivery Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $request)
                                <tr>
                                    <td>{{ $request->request_number }}</td>
                                    <td>{{ $request->requested_date->format('M d, Y') }}</td>
                                    <td>{{ $request->items()->count() }}</td>
                                    <td>
                                        @if($request->status == 'Pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @elseif($request->status == 'Approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($request->status == 'Partial')
                                            <span class="badge bg-info">Partially Approved</span>
                                        @elseif($request->status == 'Fulfilled')
                                            <span class="badge bg-primary">Fulfilled</span>
                                        @elseif($request->status == 'Rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @elseif($request->status == 'Cancelled')
                                            <span class="badge bg-secondary">Cancelled</span>
                                        @endif
                                    </td>
                                    <td>{{ $request->delivery_date ? $request->delivery_date->format('M d, Y') : 'Not scheduled' }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('outlet.order-requests.show', $request->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($request->status == 'Pending')
                                                <a href="{{ route('outlet.order-requests.edit', $request->id) }}" class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                        onclick="confirmCancel('{{ $request->id }}', '{{ $request->request_number }}')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <p class="mb-0">Showing {{ $requests->firstItem() ?? 0 }} to {{ $requests->lastItem() ?? 0 }} of {{ $requests->total() }} requests</p>
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    </div>
</main>

<!-- Cancel Confirmation Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Order Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel order request <strong id="request-number"></strong>?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <form id="cancelForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-danger">Cancel Request</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function confirmCancel(id, requestNumber) {
        document.getElementById('request-number').textContent = requestNumber;
        document.getElementById('cancelForm').action = `/outlet/order-requests/${id}/cancel`;
        var modal = new bootstrap.Modal(document.getElementById('cancelModal'));
        modal.show();
    }
</script>
@endsection
