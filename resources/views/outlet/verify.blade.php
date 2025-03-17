@extends('layouts.outlet')

@section('title', 'Verify Token - GasByGas')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card verification-card">
                <div class="card-header">
                    <h3>
                        <i class="fas fa-qrcode me-2"></i> Token Verification
                    </h3>
                    <p class="text-muted mb-0">Scan or enter token number to verify and process customer pickup</p>
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

                    <div class="row">
                        <div class="col-md-6 mb-4 mb-md-0">
                            <!-- QR Scanner Area -->
                            <div class="scanner-container">
                                <div class="scanner-header">
                                    <h5><i class="fas fa-camera me-2"></i> Scan Token QR Code</h5>
                                </div>
                                <div class="scanner-area" id="scanner">
                                    <div class="scanner-placeholder">
                                        <i class="fas fa-qrcode"></i>
                                        <p>Camera will appear here</p>
                                    </div>
                                </div>
                                <div class="scanner-controls mt-3">
                                    <button id="startButton" class="btn btn-primary">
                                        <i class="fas fa-play me-2"></i> Start Scanner
                                    </button>
                                    <button id="stopButton" class="btn btn-secondary d-none">
                                        <i class="fas fa-stop me-2"></i> Stop Scanner
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <!-- Manual Entry Form -->
                            <div class="manual-entry">
                                <div class="manual-entry-header">
                                    <h5><i class="fas fa-keyboard me-2"></i> Manual Token Entry</h5>
                                </div>
                                <form action="{{ route('outlet.tokens.verify-submit') }}" method="POST">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label for="token_number">Token Number</label>
                                        <input type="text" id="token_number" name="token_number"
                                               class="form-control form-control-lg @error('token_number') is-invalid @enderror"
                                               placeholder="Enter token number (e.g., T12345)"
                                               value="{{ old('token_number') }}">
                                        @error('token_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="customer_nic">Customer NIC/ID (Optional)</label>
                                        <input type="text" id="customer_nic" name="customer_nic"
                                               class="form-control @error('customer_nic') is-invalid @enderror"
                                               placeholder="For additional verification"
                                               value="{{ old('customer_nic') }}">
                                        @error('customer_nic')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="fas fa-search me-2"></i> Verify Token
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Token Verification Result (shows if token is found) -->
                    @if(isset($token))
                    <div class="token-result mt-4">
                        <div class="verification-result {{ $token->is_active ? 'result-valid' : 'result-invalid' }}">
                            <div class="result-header">
                                <h4>
                                    @if($token->is_active && $token->status === 'Valid')
                                        <i class="fas fa-check-circle me-2"></i> Valid Token
                                    @else
                                        <i class="fas fa-times-circle me-2"></i> Invalid Token
                                    @endif
                                </h4>
                                <span class="token-number">{{ $token->token_number }}</span>
                            </div>

                            <div class="result-details">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-borderless table-sm mb-0">
                                            <tr>
                                                <th>Customer:</th>
                                                <td>{{ $token->user->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Phone:</th>
                                                <td>{{ $token->user->phone }}</td>
                                            </tr>
                                            <tr>
                                                <th>Gas Type:</th>
                                                <td>{{ $token->gasRequest->gasType->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Quantity:</th>
                                                <td>{{ $token->gasRequest->quantity }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-borderless table-sm mb-0">
                                            <tr>
                                                <th>Created:</th>
                                                <td>{{ $token->created_at->format('d M, Y') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Valid Until:</th>
                                                <td>{{ $token->valid_until->format('d M, Y') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Amount:</th>
                                                <td>Rs. {{ number_format($token->gasRequest->amount, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <th>Status:</th>
                                                <td>
                                                    <span class="badge {{ $token->status === 'Valid' ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $token->status }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            @if($token->is_active && $token->status === 'Valid')
                            <div class="result-actions">
                                <form action="{{ route('outlet.tokens.process-confirmed', $token->id) }}" method="POST">
                                    @csrf
                                    <div class="row g-3 align-items-center">
                                        <div class="col-md-5">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="empty_cylinder" name="empty_cylinder" value="1" required>
                                                <label class="form-check-label" for="empty_cylinder">
                                                    Empty Cylinder Received
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="payment_received" name="payment_received" value="1" required>
                                                <label class="form-check-label" for="payment_received">
                                                    Payment Received
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-success w-100">
                                                <i class="fas fa-check me-2"></i> Confirm Pickup
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            @elseif($token->status === 'Expired')
                            <div class="result-actions text-center">
                                <div class="alert alert-warning mb-0">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    This token has expired on {{ $token->valid_until->format('d M, Y') }}
                                </div>
                            </div>
                            @elseif($token->status === 'Used')
                            <div class="result-actions text-center">
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    This token has already been used on {{ $token->updated_at->format('d M, Y') }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('outlet.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                </a>
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

    .verification-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .scanner-container, .manual-entry {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        height: 100%;
    }

    .scanner-header, .manual-entry-header {
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }

    .scanner-area {
        height: 240px;
        background-color: #000;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        overflow: hidden;
    }

    .scanner-placeholder {
        text-align: center;
    }

    .scanner-placeholder i {
        font-size: 40px;
        margin-bottom: 10px;
        opacity: 0.5;
    }

    .token-result {
        background-color: #fff;
        border-radius: 10px;
        margin-top: 30px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .verification-result {
        overflow: hidden;
    }

    .result-valid {
        border-left: 5px solid #2ecc71;
    }

    .result-invalid {
        border-left: 5px solid #e74c3c;
    }

    .result-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        background-color: #f8f9fa;
    }

    .result-details {
        padding: 20px;
        background-color: white;
    }

    .result-actions {
        padding: 15px 20px;
        background-color: #f8f9fa;
        border-top: 1px solid #eee;
    }

    .token-number {
        font-weight: bold;
        font-family: monospace;
        padding: 6px 10px;
        background: var(--primary-yellow);
        border-radius: 4px;
    }

    th {
        width: 120px;
    }
</style>
@endsection

@section('scripts')
<script>
    // This is just a placeholder for QR scanner functionality
    // In a real implementation, you would use a library like instascan or html5-qrcode

    document.addEventListener('DOMContentLoaded', function() {
        const startButton = document.getElementById('startButton');
        const stopButton = document.getElementById('stopButton');
        const scanner = document.getElementById('scanner');

        startButton.addEventListener('click', function() {
            startButton.classList.add('d-none');
            stopButton.classList.remove('d-none');

            // Replace placeholder with camera feed (simulated here)
            scanner.innerHTML = '<div class="alert alert-info">Camera access would be requested here in the actual implementation.</div>';

            // In real implementation, you would initialize the scanner here
            // and set up callback for successful scans

            // Simulated successful scan after 5 seconds for demonstration
            setTimeout(function() {
                document.getElementById('token_number').value = 'T12345';
            }, 5000);
        });

        stopButton.addEventListener('click', function() {
            stopButton.classList.add('d-none');
            startButton.classList.remove('d-none');

            // Reset scanner area to placeholder
            scanner.innerHTML = `
                <div class="scanner-placeholder">
                    <i class="fas fa-qrcode"></i>
                    <p>Camera will appear here</p>
                </div>
            `;

            // In real implementation, you would stop the scanner here
        });
    });
</script>
@endsection
