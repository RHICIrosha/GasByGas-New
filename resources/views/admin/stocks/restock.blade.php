@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Restock {{ $stock->gasType->name }} Stock</h3>
                </div>
                <form action="{{ route('admin.stocks.restock', $stock->id) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Current Total Quantity</label>
                                    <input type="text" class="form-control"
                                           value="{{ number_format($stock->total_quantity, 2) }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Current Available Quantity</label>
                                    <input type="text" class="form-control"
                                           value="{{ number_format($stock->available_quantity, 2) }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="quantity">Restock Quantity</label>
                                    <input type="number"
                                           name="quantity"
                                           id="quantity"
                                           class="form-control @error('quantity') is-invalid @enderror"
                                           min="0.01"
                                           step="0.01"
                                           required
                                           value="{{ old('quantity') }}">
                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="reserve_for_head_office">Reserve for Head Office</label>
                                    <input type="number"
                                           name="reserve_for_head_office"
                                           id="reserve_for_head_office"
                                           class="form-control @error('reserve_for_head_office') is-invalid @enderror"
                                           min="0"
                                           step="0.01"
                                           max="{{ old('quantity', 0) }}"
                                           value="{{ old('reserve_for_head_office', $currentReserved) }}">
                                    @error('reserve_for_head_office')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Optional: Quantity to reserve for head office (max: restocked quantity)
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus-circle mr-1"></i> Restock
                        </button>
                        <a href="{{ route('admin.stocks.index') }}" class="btn btn-secondary ml-2">
                            <i class="fas fa-arrow-left mr-1"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('quantity');
    const reserveInput = document.getElementById('reserve_for_head_office');

    // Dynamically update max reserve based on quantity
    quantityInput.addEventListener('input', function() {
        reserveInput.max = this.value;

        // Ensure reserve doesn't exceed quantity
        if (parseFloat(reserveInput.value) > parseFloat(this.value)) {
            reserveInput.value = this.value;
        }
    });
});
</script>
@endpush
