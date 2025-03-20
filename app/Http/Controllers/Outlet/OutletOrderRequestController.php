<?php

namespace App\Http\Controllers\Outlet;

use App\Http\Controllers\Controller;
use App\Models\GasType;
use App\Models\OutletOrderRequest;
use App\Models\OutletOrderRequestItem;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OutletOrderRequestController extends Controller
{
    /**
     * Display a listing of the requests.
     */
    public function index()
    {
        // Get the current user's outlet
        $user = Auth::user();
        $outlet = $user->managedOutlet;

        if (!$outlet) {
            return redirect()->route('outlet.dashboard')
                ->with('error', 'You are not assigned to any outlet.');
        }

        // Get all order requests for this outlet
        $requests = OutletOrderRequest::where('outlet_id', $outlet->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('outlet.order-requests.index', compact('requests', 'outlet'));
    }

    /**
     * Show the form for creating a new request.
     */
    public function create()
    {
        // Get the current user's outlet
        $user = Auth::user();
        $outlet = $user->managedOutlet;

        if (!$outlet) {
            return redirect()->route('outlet.dashboard')
                ->with('error', 'You are not assigned to any outlet.');
        }

        // Get all active gas types
        $gasTypes = GasType::where('is_active', true)->get();

        return view('outlet.order-requests.create', compact('outlet', 'gasTypes'));
    }

    /**
     * Store a newly created request in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'notes' => 'nullable|string',
            'requested_date' => 'required|date',
            'gas_type_id' => 'required|array',
            'gas_type_id.*' => 'exists:gas_types,id',
            'quantity' => 'required|array',
            'quantity.*' => 'integer|min:1',
        ]);

        // Check if outlet belongs to the current user
        $user = Auth::user();
        $outlet = Outlet::findOrFail($request->outlet_id);

        if ($outlet->manager_id !== $user->id) {
            return redirect()->route('outlet.order-requests.index')
                ->with('error', 'You are not authorized to create requests for this outlet.');
        }

        // Begin transaction
        DB::beginTransaction();

        try {
            // Create order request
            $orderRequest = new OutletOrderRequest();
            $orderRequest->outlet_id = $request->outlet_id;
            $orderRequest->manager_id = $user->id;
            $orderRequest->status = 'Pending';
            $orderRequest->notes = $request->notes;
            $orderRequest->requested_date = $request->requested_date;
            $orderRequest->generateRequestNumber();
            $orderRequest->save();

            // Create order request items
            $gasTypeIds = $request->gas_type_id;
            $quantities = $request->quantity;

            foreach ($gasTypeIds as $index => $gasTypeId) {
                if (isset($quantities[$index]) && $quantities[$index] > 0) {
                    OutletOrderRequestItem::create([
                        'outlet_order_request_id' => $orderRequest->id,
                        'gas_type_id' => $gasTypeId,
                        'quantity_requested' => $quantities[$index],
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('outlet.order-requests.show', $orderRequest->id)
                ->with('success', 'Order request created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create outlet order request: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create order request. Please try again.');
        }
    }

    /**
     * Display the specified request.
     */
    public function show($id)
    {
        // Get the current user's outlet
        $user = Auth::user();
        $outlet = $user->managedOutlet;

        if (!$outlet) {
            return redirect()->route('outlet.dashboard')
                ->with('error', 'You are not assigned to any outlet.');
        }

        // Get the order request
        $orderRequest = OutletOrderRequest::with(['items.gasType', 'manager', 'approver'])
            ->where('outlet_id', $outlet->id)
            ->findOrFail($id);

        return view('outlet.order-requests.show', compact('orderRequest', 'outlet'));
    }

    /**
     * Show the form for editing the specified request.
     */
    public function edit($id)
    {
        // Get the current user's outlet
        $user = Auth::user();
        $outlet = $user->managedOutlet;

        if (!$outlet) {
            return redirect()->route('outlet.dashboard')
                ->with('error', 'You are not assigned to any outlet.');
        }

        // Get the order request
        $orderRequest = OutletOrderRequest::with('items.gasType')
            ->where('outlet_id', $outlet->id)
            ->where('status', 'Pending') // Only allow editing of pending requests
            ->findOrFail($id);

        // Get all active gas types
        $gasTypes = GasType::where('is_active', true)->get();

        return view('outlet.order-requests.edit', compact('orderRequest', 'outlet', 'gasTypes'));
    }

    /**
     * Update the specified request in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'notes' => 'nullable|string',
            'requested_date' => 'required|date',
            'gas_type_id' => 'required|array',
            'gas_type_id.*' => 'exists:gas_types,id',
            'quantity' => 'required|array',
            'quantity.*' => 'integer|min:1',
        ]);

        // Get the current user's outlet
        $user = Auth::user();
        $outlet = $user->managedOutlet;

        if (!$outlet) {
            return redirect()->route('outlet.dashboard')
                ->with('error', 'You are not assigned to any outlet.');
        }

        // Get the order request
        $orderRequest = OutletOrderRequest::where('outlet_id', $outlet->id)
            ->where('status', 'Pending') // Only allow editing of pending requests
            ->findOrFail($id);

        // Begin transaction
        DB::beginTransaction();

        try {
            // Update order request
            $orderRequest->notes = $request->notes;
            $orderRequest->requested_date = $request->requested_date;
            $orderRequest->save();

            // Delete existing items
            $orderRequest->items()->delete();

            // Create new items
            $gasTypeIds = $request->gas_type_id;
            $quantities = $request->quantity;

            foreach ($gasTypeIds as $index => $gasTypeId) {
                if (isset($quantities[$index]) && $quantities[$index] > 0) {
                    OutletOrderRequestItem::create([
                        'outlet_order_request_id' => $orderRequest->id,
                        'gas_type_id' => $gasTypeId,
                        'quantity_requested' => $quantities[$index],
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('outlet.order-requests.show', $orderRequest->id)
                ->with('success', 'Order request updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update outlet order request: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update order request. Please try again.');
        }
    }

    /**
     * Cancel the specified request.
     */
    public function cancel($id)
    {
        // Get the current user's outlet
        $user = Auth::user();
        $outlet = $user->managedOutlet;

        if (!$outlet) {
            return redirect()->route('outlet.dashboard')
                ->with('error', 'You are not assigned to any outlet.');
        }

        // Get the order request
        $orderRequest = OutletOrderRequest::where('outlet_id', $outlet->id)
            ->where('status', 'Pending') // Only allow cancellation of pending requests
            ->findOrFail($id);

        // Update status to Cancelled
        $orderRequest->status = 'Cancelled';
        $orderRequest->save();

        return redirect()->route('outlet.order-requests.index')
            ->with('success', 'Order request cancelled successfully.');
    }
}
