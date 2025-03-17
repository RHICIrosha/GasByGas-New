@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Outlet Management</h1>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card summary-card bg-primary text-white">
                <div class="card-body">
                    <div class="value">{{ $stats['total'] }}</div>
                    <div class="label">Total Outlets</div>
                    <i class="icon fas fa-store"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Outlet List Card -->
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold">All Outlets</h6>
            <a href="{{ route('admin.outlets.create') }}" class="btn btn-custom">
                <i class="fas fa-plus"></i> Add New Outlet
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Contact</th>
                            <th>Manager</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($outlets as $outlet)
                        <tr>
                            <td>{{ $outlet->id }}</td>
                            <td>{{ $outlet->name }}</td>
                            <td>{{ $outlet->address }}</td>
                            <td>{{ $outlet->contact_number }}</td>
                            <td>
                                @if($outlet->manager_id)
                                    <div>
                                        <strong>{{ $outlet->manager_name ?? 'Unknown' }}</strong><br>
                                        <small class="text-muted">{{ $outlet->manager_email ?? '' }}</small>
                                    </div>
                                @else
                                    <button class="btn btn-sm btn-outline-custom" data-bs-toggle="modal" data-bs-target="#assignManagerModal" data-outlet-id="{{ $outlet->id }}">
                                        <i class="fas fa-user-plus"></i> Assign Manager
                                    </button>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.outlets.edit', $outlet->id) }}" class="btn btn-sm btn-custom">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.outlets.edit', $outlet->id) }}" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Assign Manager Modal -->
<div class="modal fade" id="assignManagerModal" tabindex="-1" aria-labelledby="assignManagerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignManagerModalLabel">Assign Manager to Outlet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="assignManagerForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="manager_id" class="form-label">Select Manager</label>
                        <select class="form-select" id="manager_id" name="manager_id" required>
                            <option value="">-- Select a Manager --</option>
                            @foreach($managers as $manager)
                                <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-custom">Assign Manager</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle assign manager modal
    const assignManagerModal = document.getElementById('assignManagerModal');
    if (assignManagerModal) {
        assignManagerModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const outletId = button.getAttribute('data-outlet-id');

            // Update form action URL
            const form = document.getElementById('assignManagerForm');
            form.action = `/admin/outlets/${outletId}/assign-manager`;
        });
    }
});
</script>
@endsection
