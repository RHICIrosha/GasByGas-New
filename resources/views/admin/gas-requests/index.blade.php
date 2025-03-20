@extends('admin.layouts.app')

@section('title', 'Gas Requests Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Gas Requests</h3>
                    <div class="card-tools">
                        <form action="{{ route('admin.gas-requests.index') }}" method="GET" class="form-inline">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" name="search" class="form-control float-right" placeholder="Search by request #" value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /.card-header -->

                <div class="card-body">
                    <div class="mb-3">
                        <form action="{{ route('admin.gas-requests.index') }}" method="GET" class="form-inline">
                            <div class="form-group mr-2">
                                <select name="status" class="form-control">
                                    <option value="">All Statuses</option>
                                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="Ready for Pickup" {{ request('status') == 'Ready for Pickup' ? 'selected' : '' }}>Ready for Pickup</option>
                                    <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <div class="form-group mr-2">
                                <select name="outlet_id" class="form-control">
                                    <option value="">All Outlets</option>
                                    @foreach($outlets as $outlet)
                                        <option value="{{ $outlet->id }}" {{ request('outlet_id') == $outlet->id ? 'selected' : '' }}>
                                            {{ $outlet->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mr-2">
                                <input type="date" name="from_date" class="form-control" placeholder="From Date" value="{{ request('from_date') }}">
                            </div>
                            <div class="form-group mr-2">
                                <input type="date" name="to_date" class="form-control" placeholder="To Date" value="{{ request('to_date') }}">
                            </div>
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('admin.gas-requests.index') }}" class="btn btn-default ml-2">Reset</a>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Request Number</th>
                                    <th>Customer</th>
                                    <th>Gas Type</th>
                                    <th>Outlet</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                    <th>Expected Pickup</th>
                                    <th>Payment & Return</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($gasRequests as $request)
                                <tr>
                                    <td>{{ $request->id }}</td>
                                    <td>{{ $request->request_number }}</td>
                                    <td>
                                        {{ $request->user->name ?? 'N/A' }}
                                        <small class="d-block text-muted">{{ $request->user->phone ?? '' }}</small>
                                    </td>
                                    <td>{{ $request->gasType->name ?? 'N/A' }}</td>
                                    <td>{{ $request->outlet->name ?? 'N/A' }}</td>
                                    <td>{{ $request->quantity }}</td>
                                    <td>
                                        <span class="badge
                                            @if($request->status == 'Pending') badge-warning
                                            @elseif($request->status == 'Approved') badge-info
                                            @elseif($request->status == 'Ready for Pickup') badge-primary
                                            @elseif($request->status == 'Completed') badge-success
                                            @elseif($request->status == 'Cancelled') badge-danger
                                            @endif">
                                            {{ $request->status }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($request->amount, 2) }}</td>
                                    <td>{{ $request->expected_pickup_date ? date('M d, Y', strtotime($request->expected_pickup_date)) : 'Not scheduled' }}</td>
                                    <td>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input"
                                                id="payment-{{ $request->id }}"
                                                {{ $request->payment_received ? 'checked' : '' }}
                                                onchange="updatePaymentStatus('{{ $request->id }}', this.checked)">
                                            <label class="custom-control-label" for="payment-{{ $request->id }}">Payment</label>
                                        </div>
                                        <div class="custom-control custom-checkbox mt-2">
                                            <input type="checkbox" class="custom-control-input"
                                                id="cylinder-{{ $request->id }}"
                                                {{ $request->empty_cylinder_returned ? 'checked' : '' }}
                                                onchange="updateCylinderStatus('{{ $request->id }}', this.checked)">
                                            <label class="custom-control-label" for="cylinder-{{ $request->id }}">Empty Returned</label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.gas-requests.show', $request->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.gas-requests.edit', $request->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="confirmDelete('{{ $request->id }}', '{{ $request->request_number }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center">No gas requests found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer clearfix">
                    <div class="float-left">
                        Showing {{ $gasRequests->firstItem() ?? 0 }} to {{ $gasRequests->lastItem() ?? 0 }} of {{ $gasRequests->total() }} entries
                    </div>
                    <div class="float-right">
                        {{ $gasRequests->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete request <span id="delete-request-number"></span>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="delete-form" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function confirmDelete(id, requestNumber) {
        document.getElementById('delete-request-number').textContent = requestNumber;
        document.getElementById('delete-form').action = "{{ route('admin.gas-requests.destroy', '') }}/" + id;
        $('#deleteModal').modal('show');
    }

    function updatePaymentStatus(requestId, status) {
        $.ajax({
            url: "{{ route('admin.gas-requests.update-payment') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                request_id: requestId,
                status: status ? 1 : 0
            },
            success: function(response) {
                toastr.success(response.message);
            },
            error: function(xhr) {
                toastr.error('An error occurred while updating payment status');
                // Revert the checkbox state
                $('#payment-' + requestId).prop('checked', !status);
            }
        });
    }

    function updateCylinderStatus(requestId, status) {
        $.ajax({
            url: "{{ route('admin.gas-requests.update-cylinder') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                request_id: requestId,
                status: status ? 1 : 0
            },
            success: function(response) {
                toastr.success(response.message);
            },
            error: function(xhr) {
                toastr.error('An error occurred while updating cylinder return status');
                // Revert the checkbox state
                $('#cylinder-' + requestId).prop('checked', !status);
            }
        });
    }
</script>
@endsection
