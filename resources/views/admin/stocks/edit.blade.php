@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Edit Stock for {{ $stock->gasType->name }}</h3>
                </div>

                <form action="{{ route('admin.stocks.update', $stock->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Gas Type</label>
                                    <input type="text" class="form-control" value="{{ $stock->gasType->name }}" readonly>
                                    <input type="hidden" name="gas_type_id" value="{{ $stock->gas_type_id }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="total_quantity">Total Quantity</label>
                                    <input type="number"
                                           class="form-control @error('total_quantity') is-invalid @enderror"
                                           id="total_quantity"
                                           name="total_quantity"
                                           value="{{ old('total_quantity', $stock->total_quantity) }}"
                                           min="{{ $stock->allocated_quantity }}"
                                           step="0.01"
                                           required>
                                    @error('total_quantity')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="available_quantity">Available Quantity</label>
                                    <input type="number"
                                           class="form-control @error('available_quantity') is-invalid @enderror"
                                           id="available_quantity"
                                           name="available_quantity"
                                           value="{{ old('available_quantity', $stock->available_quantity) }}"
                                           min="0"
                                           step="0.01"
                                           required>
                                    @error('available_quantity')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="allocated_quantity">Allocated Quantity</label>
                                    <input type="text"
                                           class="form-control"
                                           value="{{ $stock->allocated_quantity }}"
                                           readonly>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="minimum_stock_level">Minimum Stock Level</label>
                                    <input type="number"
                                           class="form-control @error('minimum_stock_level') is-invalid @enderror"
                                           id="minimum_stock_level"
                                           name="minimum_stock_level"
                                           value="{{ old('minimum_stock_level', $stock->minimum_stock_level) }}"
                                           min="0"
                                           step="0.01"
                                           required>
                                    @error('minimum_stock_level')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Current Status</label>
                                    <input type="text"
                                           class="form-control text-{{ $stock->status == 'critical' ? 'danger' : ($stock->status == 'low' ? 'warning' : 'success') }}"
                                           value="{{ ucfirst($stock->status) }}"
                                           readonly>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes (Optional)</label>
                            <textarea
                                class="form-control"
                                id="notes"
                                name="notes"
                                rows="3"
                                maxlength="500">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Stock
                        </button>
                        <a href="{{ route('admin.stocks.index') }}" class="btn btn-secondary ml-2">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const totalQuantityInput = document.getElementById('total_quantity');
    const availableQuantityInput = document.getElementById('available_quantity');

    // Dynamic validation
    totalQuantityInput.addEventListener('input', function() {
        const allocatedQuantity = {{ $stock->allocated_quantity }};
        const totalQuantity = parseFloat(this.value) || 0;

        if (totalQuantity < allocatedQuantity) {
            this.setCustomValidity('Total quantity cannot be less than allocated quantity');
        } else {
            this.setCustomValidity('');
        }

        // Update max for available quantity
        availableQuantityInput.max = totalQuantity - allocatedQuantity;
    });

    availableQuantityInput.addEventListener('input', function() {
        const totalQuantity = parseFloat(totalQuantityInput.value) || 0;
        const allocatedQuantity = {{ $stock->allocated_quantity }};

        if (parseFloat(this.value) > (totalQuantity - allocatedQuantity)) {
            this.setCustomValidity('Available quantity exceeds total minus allocated');
        } else {
            this.setCustomValidity('');
        }
    });
});
</script>
@endsection
