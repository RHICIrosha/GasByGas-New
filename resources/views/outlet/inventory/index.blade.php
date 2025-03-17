@extends('layouts.outlet')

@section('title', 'Inventory Management - GasByGas')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="page-title">
                <i class="fas fa-boxes me-2"></i> Inventory Management
            </h2>
            <p class="text-muted">Manage your outlet's gas cylinder inventory</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('outlet.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Inventory Status -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Current Inventory Status</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="inventory-summary mb-4">
                        <div class="row">
                            @foreach($inventorySummary as $summary)
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="inventory-item-card {{ $summary->status_class }}">
                                    <div class="inventory-item-icon">
                                        <i class="fas fa-gas-pump"></i>
                                    </div>
                                    <div class="inventory-item-details">
                                        <h5>{{ $summary->gas_type_name }}</h5>
                                        <div class="inventory-item-count">
                                            <span class="count">{{ $summary->quantity }}</span>
                                            <small>cylinders</small>
                                        </div>
                                        <div class="inventory-item-status">
                                            <span class="badge {{ $summary->badge_class }}">{{ $summary->status }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <form action="{{ route('outlet.inventory.update') }}" method="POST">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-bordered inventory-table">
                                <thead>
                                    <tr>
                                        <th>Gas Type</th>
                                        <th>Weight/Size</th>
                                        <th>Current Stock</th>
                                        <th>Update Stock</th>
                                        <th>Price (Rs.)</th>
                                        <th>Reorder Level</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($inventory as $item)
                                    <tr>
                                        <td>{{ $item->gasType->name }}</td>
                                        <td>{{ $item->gasType->weight }} kg</td>
                                        <td class="current-stock">{{ $item->quantity }}</td>
                                        <td>
                                            <div class="input-group">
                                                <input type="hidden" name="inventory_id[]" value="{{ $item->id }}">
                                                <input type="number" name="quantity[]" class="form-control stock-input"
                                                       value="{{ $item->quantity }}" min="0" max="999">
                                                <button type="button" class="btn btn-outline-secondary decrement-btn">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary increment-btn">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td>{{ number_format($item->gasType->price, 2) }}</td>
                                        <td>
                                            <input type="number" name="reorder_level[]" class="form-control reorder-input"
                                                   value="{{ $item->reorder_level }}" min="1" max="100">
                                        </td>
                                        <td>
                                            @if($item->quantity > $item->reorder_level * 2)
                                                <span class="badge bg-success">Good</span>
                                            @elseif($item->quantity > $item->reorder_level)
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

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i> Update Inventory
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory History -->
    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Inventory Updates</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Date/Time</th>
                                    <th>Gas Type</th>
                                    <th>Previous</th>
                                    <th>New</th>
                                    <th>Change</th>
                                    <th>Updated By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($inventoryLogs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('d M, Y H:i') }}</td>
                                    <td>{{ $log->gasType->name }}</td>
                                    <td>{{ $log->previous_quantity }}</td>
                                    <td>{{ $log->new_quantity }}</td>
                                    <td>
                                        @php
                                            $change = $log->new_quantity - $log->previous_quantity;
                                            $class = $change > 0 ? 'text-success' : ($change < 0 ? 'text-danger' : 'text-secondary');
                                            $sign = $change > 0 ? '+' : '';
                                        @endphp
                                        <span class="{{ $class }}">{{ $sign . $change }}</span>
                                    </td>
                                    <td>{{ $log->user->name }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Record Inventory Change</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('outlet.inventory.record-change') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label>Gas Type</label>
                            <select name="gas_type_id" class="form-control" required>
                                <option value="">Select Gas Type</option>
                                @foreach($gasTypes as $gasType)
                                    <option value="{{ $gasType->id }}">{{ $gasType->name }} ({{ $gasType->weight }} kg)</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label>Change Type</label>
                            <select name="change_type" class="form-control" required>
                                <option value="receive">Receive New Stock</option>
                                <option value="return">Return Empty Cylinders</option>
                                <option value="adjustment">Stock Adjustment</option>
                                <option value="damage">Damaged/Unusable</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label>Quantity</label>
                            <input type="number" name="quantity" class="form-control" required min="1" max="999">
                        </div>

                        <div class="form-group mb-3">
                            <label>Notes (Optional)</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-plus-circle me-2"></i> Record Change
                        </button>
                    </form>
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

    .inventory-item-card {
        border-radius: 10px;
        padding: 15px;
        height: 100%;
        display: flex;
        align-items: center;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .inventory-item-good {
        background-color: rgba(46, 204, 113, 0.1);
        border-left: 4px solid #2ecc71;
    }

    .inventory-item-low {
        background-color: rgba(255, 193, 7, 0.1);
        border-left: 4px solid #ffc107;
    }

    .inventory-item-critical {
        background-color: rgba(231, 76, 60, 0.1);
        border-left: 4px solid #e74c3c;
    }

    .inventory-item-icon {
        font-size: 24px;
        margin-right: 15px;
        width: 50px;
        height: 50px;
        background: #f8f9fa;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .inventory-item-details {
        flex: 1;
    }

    .inventory-item-details h5 {
        margin-bottom: 5px;
        font-size: 16px;
    }

    .inventory-item-count {
        margin-bottom: 5px;
    }

    .inventory-item-count .count {
        font-size: 20px;
        font-weight: bold;
    }

    .stock-input {
        text-align: center;
    }

    .inventory-table th {
        background-color: #f8f9fa;
    }

    .reorder-input {
        width: 80px;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handling increment/decrement buttons
        document.querySelectorAll('.increment-btn').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.parentElement.querySelector('.stock-input');
                let value = parseInt(input.value, 10);
                input.value = isNaN(value) ? 1 : value + 1;
            });
        });

        document.querySelectorAll('.decrement-btn').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.parentElement.querySelector('.stock-input');
                let value = parseInt(input.value, 10);
                input.value = isNaN(value) || value <= 0 ? 0 : value - 1;
            });
        });

        // Highlight changes as user edits
        document.querySelectorAll('.stock-input').forEach(input => {
            input.addEventListener('change', function() {
                const currentStock = parseInt(this.closest('tr').querySelector('.current-stock').textContent, 10);
                const newStock = parseInt(this.value, 10);

                if (newStock !== currentStock) {
                    this.classList.add('bg-warning', 'text-dark');
                } else {
                    this.classList.remove('bg-warning', 'text-dark');
                }
            });
        });
    });
</script>
@endsection
