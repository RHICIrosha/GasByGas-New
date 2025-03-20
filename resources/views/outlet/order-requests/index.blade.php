@extends('layouts.outlet')

@section('title', 'Outlet Stock Requests')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <a href="{{ route('outlet.order-requests.create') }}">kjhgfdsr</a>
                <div class="card-header d-flex justify-content-between align-items-center">

                <div class="card-body">
                    {{-- Rest of the existing view remains the same --}}
                    {{-- Filters --}}
                    <form action="{{ route('outlet.order-requests.index') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="status" class="form-control">
                                    <option value="">All Statuses</option>
                                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                    <option value="Partial" {{ request('status') == 'Partial' ? 'selected' : '' }}>Partially Approved</option>
                                    <option value="Fulfilled" {{ request('status') == 'Fulfilled' ? 'selected' : '' }}>Fulfilled</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="outlet_id" class="form-control">
                                    <option value="">All Outlets</option>
                                    @foreach($outlets as $outlet)
                                        <option value="{{ $outlet->id }}"
                                            {{ request('outlet_id') == $outlet->id ? 'selected' : '' }}>
                                            {{ $outlet->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="date" name="from_date" class="form-control"
                                       placeholder="From Date"
                                       value="{{ request('from_date') }}">
                            </div>
                            <div class="col-md-3">
                                <input type="date" name="to_date" class="form-control"
                                       placeholder="To Date"
                                       value="{{ request('to_date') }}">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <a href="{{ route('outlet.order-requests.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-sync"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    {{-- Rest of the view remains the same --}}
                    {{-- Table and Pagination sections --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')

@endsection

@section('styles')
<style>
    .table-responsive {
        margin-top: 20px;
    }

    .btn-group .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
</style>
@endsection
