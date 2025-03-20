@extends('layouts.outlet')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Create Outlet Order') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('outlet.order-requests.store') }}">
                            @csrf

                            <!-- ... -->

                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-md-right">{{ __('Order Items') }}</label>

                                <div class="col-md-6">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Gas Type') }}</th>
                                                <th>{{ __('Quantity') }}</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="orderItemsBody">
                                            @foreach ($gasTypes as $gasType)
                                                <tr>
                                                    <td>
                                                        <input type="hidden" name="gas_type_id[]" value="{{ $gasType->id }}">
                                                        {{ $gasType->name }}
                                                    </td>
                                                    <td>
                                                        <input type="number" name="quantity_requested[{{ $gasType->id }}]" class="form-control" min="0" value="0" required>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                {{ __('Create Order') }}
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
