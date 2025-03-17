<?php

namespace App\Http\Controllers;

use App\Models\GasRequest;
use App\Models\GasType;
use App\Models\Outlet;
use App\Models\Token;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PHPUnit\Event\Code\Test;

class OrderController extends Controller
{
    /**
     * Show order form
     */
    public function create()
    {$user = Auth::user();
        $gasTypes = GasType::all();
        $outlets = Outlet::where('has_stock', true)
                        ->where('is_accepting_orders', true)
                        ->get();

        // Count pending orders for the user
        $pendingOrdersCount = GasRequest::where('user_id', $user->id)
                                ->whereIn('status', ['Pending', 'Confirmed'])
                                ->count();

        return view('orders.create', compact(
            'gasTypes',
            'outlets',
            'pendingOrdersCount'
        ));
    }

    /**
     * Store a new order
     */
/**
 * Store a new order
 */
public function store(Request $request)
{
    $user = Auth::user();

    // Validate request
    $maxQuantity = ($user->user_type === 'business') ? 20 : 2;

    $request->validate([
        'gas_type_id' => 'required|exists:gas_types,id',
        'outlet_id' => 'required|exists:outlets,id',
        'quantity' => "required|integer|min:1|max:{$maxQuantity}",
        'special_instructions' => 'nullable|string|max:255',
        'terms' => 'required|accepted',
    ]);

    // Check if outlet is available and accepting orders
    $outlet = Outlet::findOrFail($request->outlet_id);
    if (!$outlet->has_stock || !$outlet->is_accepting_orders) {
        return redirect()->back()->with('error', 'This outlet is not currently accepting orders or is out of stock.');
    }

    // Check order limits based on user user_type
    $pendingOrdersCount = GasRequest::where('user_id', $user->id)
                            ->whereIn('status', ['Pending', 'Confirmed'])
                            ->count();

    if ($user->user_type === 'business') {
        if ($pendingOrdersCount >= 100) {
            return redirect()->back()->with('error', 'You have reached the maximum limit of 100 pending orders.');
        }
    } else {
        if ($pendingOrdersCount >= 1) {
            return redirect()->back()->with('error', 'You already have a pending order. Please wait until it is completed.');
        }
    }

    // Get price from gas type and calculate total based on quantity
    $gasType = GasType::findOrFail($request->gas_type_id);
    $unitPrice = $gasType->price;
    $quantity = (int) $request->quantity;
    $totalAmount = $unitPrice * $quantity;

    // Begin transaction
    DB::beginTransaction();

    try {
        // Create gas request
        $gasRequest = new GasRequest();
        $gasRequest->user_id = $user->id;
        $gasRequest->gas_type_id = $request->gas_type_id;
        $gasRequest->outlet_id = $request->outlet_id;
        $gasRequest->quantity = $quantity;
        $gasRequest->notes = $request->special_instructions;
        $gasRequest->amount = $totalAmount;
        $gasRequest->status = 'Pending';
        $gasRequest->expected_pickup_date = Carbon::now()->addDays(14);
        $gasRequest->expected_pickup_date = Carbon::now()->addDays(14);
        $gasRequest->empty_cylinder_returned = false; // Initialize empty cylinder status
        $gasRequest->payment_received = false; // Initialize payment status
        $gasRequest->generateRequestNumber();
        $gasRequest->save();

        // Create token
        $token = new Token();
        $token->gas_request_id = $gasRequest->id;
        $token->user_id = $user->id;
        $token->outlet_id = $request->outlet_id;
        $token->valid_from = Carbon::now();
        $token->valid_until = Carbon::now()->addDays(14);
        $token->is_active = true;
        $token->status = 'Valid';
        $token->generateTokenNumber();
        $token->save();

        // Prepare SMS message
        // $message = $this->buildOrderMessage($gasRequest, $token, $gasType, $outlet, $quantity, $unitPrice, $totalAmount);

        // Get phone number from auth user
        $userPhone = $user->phone;

        // Log the phone number for debugging
        Log::info("Attempting to send SMS to phone: {$userPhone}");

        // Send SMS and log success/failure
        // $smsResult = $this->sendDirectSMS($userPhone, $message);
        // if ($smsResult) {
        //     Log::info("SMS sent successfully to {$userPhone}");
        // } else {
        //     Log::warning("Failed to send SMS to {$userPhone}");
        // }
        $smsController = new SmsController();
        $smsResult = $smsController->sendOrderConfirmation(
            $user->phone,
            $token->token_number,
            $gasType->name,
            $outlet->name,
            $quantity,
            $totalAmount
        );

        if ($smsResult) {
            Log::info("SMS sent successfully to {$user->phone}");
        } else {
            Log::warning("Failed to send SMS to {$user->phone}");
        }

        DB::commit();

        return redirect()->route('orders.confirmation', $gasRequest->id)
            ->with('success', 'Your gas request has been placed successfully.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Order creation error: ' . $e->getMessage());

        return redirect()->back()
            ->with('error', 'Failed to place your order: ' . $e->getMessage())
            ->withInput();
    }
}

/**
 * Build SMS message for order notification
 */
    private function buildOrderMessage($gasRequest, $token, $gasType, $outlet, $quantity, $unitPrice, $totalAmount)
    {
        $message = "GasbyGas. Your order has been placed successfully!. Token: " . $token->token_number;

        // Add minimal order details without newlines or special formatting
        $message .= " ,QTY - " . $quantity . ", Gas Type" . $gasType->name;
        $message .= " - Pickup at " . $outlet->name;

        // Log the simple message
        Log::info("SMS message content: " . $message);

        return $message;



    }
    /**
     * Show order confirmation page
     */
    public function confirmation($id)
    {
        $gasRequest = GasRequest::with(['gasType', 'outlet'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $token = Token::where('gas_request_id', $gasRequest->id)->first();

        return view('orders.confirmation', compact('gasRequest', 'token'));
    }

    /**
     * Track an order
     */
    public function track($id)
    {
        $gasRequest = GasRequest::with(['gasType', 'outlet', 'token'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('orders.track', compact('gasRequest'));
    }




    // in here not use this funtion .created Smscontroller and add url to env
    private function sendDirectSMS($phone, $message)
    {
        try {
            $username = 'admin';
            $password = 'password';
            $url = 'http://192.168.8.123:8888/send-message';

            // Use EXACTLY the same format as your test command
            $jsonData = json_encode([
                'phoneNumber' => $phone,
                'message' => $message
            ]);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($jsonData)
            ]);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);
            $info = curl_getinfo($ch);

            curl_close($ch);

            return ($info['http_code'] >= 200 && $info['http_code'] < 300);
        } catch (\Exception $e) {
            Log::error('SMS Exception: ' . $e->getMessage());
            return false;
        }
    }
    public function history(Request $request)
    {
        $query = GasRequest::with(['gasType', 'outlet'])
            ->where('user_id', Auth::id());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('gas_type')) {
            $query->where('gas_type_id', $request->gas_type);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        // Get all gas types for filter dropdown
        $gasTypes = GasType::all();

        return view('orders.history', compact('orders', 'gasTypes'));
    }
    public function show($id)
{
    // Retrieve the gas request by ID, including related gas type and outlet
    $order = GasRequest::with(['gasType', 'outlet'])
        ->where('user_id', Auth::id())
        ->findOrFail($id);

    // Return the view with the gas request data
    return view('orders.show', compact('order'));
}
    /**
 * Cancel an order
 */
    public function cancel($id)
    {
        $order = GasRequest::where('user_id', Auth::id())
            ->where('status', 'Pending')
            ->findOrFail($id);

        $order->status = 'Cancelled';
        $order->save();

        // If there's a token, update it too
        if ($order->token) {
            $order->token->is_active = false;
            $order->token->status = 'Cancelled';
            $order->token->save();
        }

        return redirect()->route('customer.dashboard')
            ->with('success', 'Order has been cancelled successfully.');
    }
    public function processPickup($id, Request $request)
    {
        // This method would be called when an outlet manager confirms a pickup
        $order = GasRequest::findOrFail($id);

        // Update the fields added in our new migration
        $order->status = 'Completed';
        $order->actual_pickup_date = Carbon::now();
        $order->empty_cylinder_returned = $request->has('empty_cylinder');
        $order->payment_received = $request->has('payment');
        $order->save();

        // Update token status
        if ($order->token) {
            $order->token->status = 'Used';
            $order->token->is_active = false;
            $order->token->save();
        }

        return redirect()->back()->with('success', 'Order has been marked as picked up.');
    }
}
