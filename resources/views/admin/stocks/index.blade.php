```blade
<!-- admin/stocks/index.blade.php -->
@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Head Office Stock Management</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.stocks.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus mr-1"></i> Add New Stock
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Summary Statistics --}}
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-cubes"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Stock</span>
                                    <span class="info-box-number">{{ number_format($totalStock, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Available Stock</span>
                                    <span class="info-box-number">{{ number_format($availableStock, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-warehouse"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Allocated Stock</span>
                                    <span class="info-box-number">{{ number_format($allocatedStock, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Flash Messages --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    {{-- Stock Table --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="stocksTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Gas Type</th>
                                    <th>Total Quantity</th>
                                    <th>Available</th>
                                    <th>Allocated</th>
                                    <th>Status</th>
                                    <th>Last Restocked</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($stocks as $stock)
                                    <tr>
                                        <td>{{ $stock->id }}</td>
                                        <td>{{ $stock->gasType->name }}</td>
                                        <td class="text-right">{{ number_format($stock->total_quantity, 2) }}</td>
                                        <td class="text-right">{{ number_format($stock->available_quantity, 2) }}</td>
                                        <td class="text-right">{{ number_format($stock->allocated_quantity, 2) }}</td>
                                        <td>
                                            @switch($stock->status)
                                                @case('normal')
                                                    <span class="badge bg-success">Normal</span>
                                                    @break
                                                @case('low')
                                                    <span class="badge bg-warning">Low</span>
                                                    @break
                                                @case('critical')
                                                    <span class="badge bg-danger">Critical</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>{{ $stock->last_restock_date ? \Carbon\Carbon::parse($stock->last_restock_date)->format('Y-m-d H:i') : 'N/A' }}</td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.stocks.edit', $stock->id) }}" class="btn btn-sm btn-outline-info" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('admin.stocks.restock', $stock->id) }}" class="btn btn-sm btn-outline-success" title="Restock">
                                                    <i class="fas fa-plus-circle"></i>
                                                </a>
                                                <form action="{{ route('admin.stocks.destroy', $stock->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger delete-stock" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <div class="alert alert-info">
                                                No stock records found.
                                                <a href="{{ route('admin.stocks.create') }}" class="alert-link">Create your first stock entry</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="stock-status-summary">
                                <span class="badge bg-success mr-2">Normal: {{ $normalCount }}</span>
                                <span class="badge bg-warning mr-2">Low: {{ $lowCount }}</span>
                                <span class="badge bg-danger">Critical: {{ $criticalCount }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Confirm deletion
    const deleteButtons = document.querySelectorAll('.delete-stock');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this stock record? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endpush

