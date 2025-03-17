@extends('layouts.admin')


@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Edit Outlet: {{ $outlet->name }}</h1>
        <a href="{{ route('admin.outlets.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Outlets
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold">Outlet Details</h6>
            <span class="badge bg-primary">ID: {{ $outlet->id }}</span>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.outlets.update', $outlet->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="code" class="form-label">Outlet Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror"
                               id="code" name="code" value="{{ old('code', $outlet->code) }}" required>
                        <div class="form-text">Unique identifier for this outlet</div>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Outlet Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name', $outlet->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('address') is-invalid @enderror"
                             id="address" name="address" rows="3" required>{{ old('address', $outlet->address) }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="contact_number" class="form-label">Contact Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('contact_number') is-invalid @enderror"
                           id="contact_number" name="contact_number" value="{{ old('contact_number', $outlet->contact_number) }}" required>
                    @error('contact_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="manager_id" class="form-label">Outlet Manager</label>
                    <select class="form-select @error('manager_id') is-invalid @enderror" id="manager_id" name="manager_id">
                        <option value="">-- No Manager Assigned --</option>
                        @foreach($managers as $manager)
                            <option value="{{ $manager->id }}" {{ (old('manager_id', $outlet->manager_id) == $manager->id) ? 'selected' : '' }}>
                                {{ $manager->name }} ({{ $manager->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('manager_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="has_stock" name="has_stock" value="1"
                                  {{ old('has_stock', $outlet->has_stock) == 1 ? 'checked' : '' }}>
                            <label class="form-check-label" for="has_stock">Has Stock Available</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_accepting_orders" name="is_accepting_orders" value="1"
                                  {{ old('is_accepting_orders', $outlet->is_accepting_orders) == 1 ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_accepting_orders">Is Accepting Orders</label>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteOutletModal">
                        <i class="fas fa-trash"></i> Delete Outlet
                    </button>
                    <button type="submit" class="btn btn-custom">Update Outlet</button>
                </div>
            </form>
        </div>
    </div>

    @if($outlet->manager_id)
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold">Current Manager Information</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">Name:</th>
                            <td>{{ $outlet->manager_name ?? 'Not available' }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $outlet->manager_email ?? 'Not available' }}</td>
                        </tr>
                        <tr>
                            <th>Manager ID:</th>
                            <td>{{ $outlet->manager_id }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6 d-flex align-items-center justify-content-center">
                    {{-- <a href="{{ route('admin.users.edit', $outlet->manager_id) }}" class="btn btn-outline-primary">
                        <i class="fas fa-user-edit"></i> View Manager Profile
                    </a> --}}
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Delete Outlet Confirmation Modal -->
<div class="modal fade" id="deleteOutletModal" tabindex="-1" aria-labelledby="deleteOutletModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteOutletModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this outlet? This action cannot be undone.</p>
                <p class="text-danger"><strong>Warning:</strong> Deleting this outlet will also remove all associated records including inventory and order history.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.outlets.destroy', $outlet->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Permanently</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
