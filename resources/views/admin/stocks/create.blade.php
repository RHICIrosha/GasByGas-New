<!-- resources/views/admin/stocks/create.blade.php -->
@extends('layouts.admin')

@section('title', 'Add Stock')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.stocks.index') }}">Stock Management</a></li>
    <li class="breadcrumb-item active">Add Stock</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold">Add Stock</h6>
        </div>
        <form action="{{ route('admin.stocks.store') }}" method="POST">
            @csrf
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-group">
                    <label for="gas_type_id">Gas Type</label>
                    <select name="gas_type_id" id="gas_type_id" class="form-control" required>
                        <option value="">Select Gas Type</option>
                        @foreach($gasTypes as $gasType)
                            <option value="{{ $gasType->id }}">
                                {{ $gasType->name }}
                                @if(isset($existingStocks[$gasType->id]))
                                    (Existing Stock: {{ $existingStocks[$gasType->id]->total_quantity }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="total_quantity" class="form-label">Quantity to Add</label>
                            <input type="number" name="total_quantity" id="total_quantity" class="form-control @error('total_quantity') is-invalid @enderror" value="{{ old('total_quantity') }}" min="1" required>
                            @error('total_quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="reserve_for_head_office" class="form-label">Reserve for Head Office</label>
                            <input type="number" name="reserve_for_head_office" id="reserve_for_head_office" class="form-control @error('reserve_for_head_office') is-invalid @enderror" value="{{ old('reserve_for_head_office', 0) }}" min="0">
                            @error('reserve_for_head_office')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Units to keep at head office (not available for outlet distribution)</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="minimum_stock_level" class="form-label">Minimum Stock Level</label>
                            <input type="number" name="minimum_stock_level" id="minimum_stock_level" class="form-control @error('minimum_stock_level') is-invalid @enderror" value="{{ old('minimum_stock_level', 10) }}" min="1" required>
                            @error('minimum_stock_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Alert will be shown when available stock falls below this level</small>
                        </div>
                    </div>
                </div>

                <div id="existingStockInfo" class="alert alert-info d-none">
                    <h6>Adding to Existing Stock</h6>
                    <p>You're adding more units to an existing gas type. The quantities will be combined.</p>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.stocks.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary" id="submitBtn">Add Stock</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const gasTypeSelect = document.getElementById('gas_type_id');
        const totalInput = document.getElementById('total_quantity');
        const reserveInput = document.getElementById('reserve_for_head_office');
        const existingStockInfo = document.getElementById('existingStockInfo');
        const submitBtn = document.getElementById('submitBtn');

        // Check for existing stock and update UI
        gasTypeSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption && selectedOption.getAttribute('data-existing') === 'true') {
                existingStockInfo.classList.remove('d-none');
                submitBtn.textContent = 'Add to Existing Stock';
            } else {
                existingStockInfo.classList.add('d-none');
                submitBtn.textContent = 'Add Stock';
            }
        });

        // Validate quantities
        function validateQuantities() {
            const total = parseInt(totalInput.value) || 0;
            const reserve = parseInt(reserveInput.value) || 0;

            if (reserve > total) {
                reserveInput.value = total;
            }
        }

        totalInput.addEventListener('change', validateQuantities);
        reserveInput.addEventListener('change', validateQuantities);
    });
</script>
@endsection
