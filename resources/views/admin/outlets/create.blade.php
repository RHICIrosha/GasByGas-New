@extends('layouts.admin')


@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Create New Outlet</h1>
        <a href="{{ route('admin.outlets.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Outlets
        </a>
    </div>

    <!-- Display validation errors if any -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Display error message if any -->
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Create Outlet Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold">Outlet Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.outlets.store') }}" method="POST">
                @csrf

                <div class="row">
                    <!-- Outlet Code -->
                    <div class="col-md-6 mb-3">
                        <label for="code" class="form-label">Outlet Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror"
                               id="code" name="code" value="{{ old('code') }}" required>
                        <div class="form-text">Unique identifier for this outlet (e.g., OUT001)</div>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Outlet Name -->
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Outlet Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Address -->
                <div class="mb-3">
                    <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('address') is-invalid @enderror"
                             id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Contact Number -->
                <div class="mb-3">
                    <label for="contact_number" class="form-label">Contact Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('contact_number') is-invalid @enderror"
                           id="contact_number" name="contact_number" value="{{ old('contact_number') }}" required>
                    @error('contact_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Manager -->
                <div class="mb-3">
                    <label for="manager_id" class="form-label">Outlet Manager</label>
                    <select class="form-select @error('manager_id') is-invalid @enderror"
                            id="manager_id" name="manager_id">
                        <option value="">-- Select Manager (Optional) --</option>
                        @foreach($managers as $manager)
                            <option value="{{ $manager->id }}" {{ old('manager_id') == $manager->id ? 'selected' : '' }}>
                                {{ $manager->name }} ({{ $manager->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('manager_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text mt-2">
                        {{-- <a href="{{ route('admin.users.create') }}?type=outlet_manager" class="text-primary">
                            <i class="fas fa-plus-circle"></i> Register New Manager
                        </a> --}}
                    </div>
                </div>

                <!-- Status Options -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="has_stock" name="has_stock" value="1" {{ old('has_stock', '1') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="has_stock">Has Stock Available</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_accepting_orders" name="is_accepting_orders" value="1" {{ old('is_accepting_orders', '1') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_accepting_orders">Is Accepting Orders</label>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-end">
                    <button type="reset" class="btn btn-outline-secondary me-2">Reset</button>
                    <button type="submit" class="btn btn-custom">Create Outlet</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
