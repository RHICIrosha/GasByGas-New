<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GasType;
use App\Models\HeadOfficeStock;
use App\Models\OutletOrderRequest;
use App\Models\OutletOrderRequestItem;
use App\Models\StockAllocation;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OutletOrderRequestController extends Controller
{
    /**
     * Display a listing of all outlet order requests.
     */
    public function index(Request $request)
    {
        $query = OutletOrderRequest::with(['outlet', 'manager']);

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->where('request_number', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Filter by outlet
        if ($request->has('outlet_id') && !empty($request->outlet_id)) {
            $query->where('outlet_id', $request->outlet_id);
        }

        // Date range filter
        if ($request->has('from_date') && !empty($request->from_date)) {
            $query->whereDate('requested_date', '>=', $request->from_date);
        }

        if ($request->has('to_date') && !empty($request->to_date)) {
            $query->whereDate('requested_date', '<=', $request->to_date);
        }

        // Order by latest
        $query->orderBy('created_at', 'desc');

        $orderRequests = $query->paginate(15);
        $outlets = Outlet::all();

        return view('admin.outlet-requests.index', compact('orderRequests', 'outlets'));
    }

    /**
     * Display the specified request.
     */
    public function show($id)
    {
        $orderRequest = OutletOrderRequest::with(['outlet', 'manager', 'approver', 'items.gasType'])
            ->findOrFail($id);

        // Get stock availability for each item
        $stockAvailability = [];
        foreach ($orderRequest->items as $item) {
            $headOfficeStock = HeadOfficeStock::where('gas_type_id', $item->gas_type_id)->first();

            $stockAvailability[$item->id] = [
                'available' => $headOfficeStock ? $headOfficeStock->available_quantity : 0,
                'sufficient' => $headOfficeStock ? ($headOfficeStock->available_quantity >= $item->quantity_requested) : false
            ];
        }

        return view('admin.outlet-requests.show', compact('orderRequest', 'stockAvailability'));
    }

    /**
     * Show the form for processing the order request.
     */
    public function process($id)
    {
        $orderRequest = OutletOrderRequest::with(['outlet', 'manager', 'items.gasType'])
            ->where('status', 'Pending')
            ->findOrFail($id);

        // Get stock availability for each item
        $stockAvailability = [];
        foreach ($orderRequest->items as $item) {
            $headOfficeStock = HeadOfficeStock::where('gas_type_id', $item->gas_type_id)->first();

            $stockAvailability[$item->id] = [
                'available' => $headOfficeStock ? $headOfficeStock->available_quantity : 0,
                'sufficient' => $headOfficeStock ? ($headOfficeStock->available_quantity >= $item->quantity_requested) : false
            ];
        }

        return view('admin.outlet-requests.process', compact('orderRequest', 'stockAvailability'));
    }

    /**
     * Process the order request - approve/reject items.
     */
    public function updateProcess(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'delivery_date' => 'required|date',
            'quantity_approved' => 'required|array',
            'quantity_approved.*' => 'integer|min:0',
            'notes' => 'nullable|string',
        ]);

        // Get the order request
        $orderRequest = OutletOrderRequest::with('items')
            ->where('status', 'Pending')
            ->findOrFail($id);

        // Begin transaction
        DB::beginTransaction();

        try {
            // Update each item
            $allApproved = true;
            $allRejected = true;
            $partialApproval = false;

            foreach ($orderRequest->items as $item) {
                $approvedQuantity = $request->quantity_approved[$item->id] ?? 0;

                $item->quantity_approved = $approvedQuantity;
                $item->save();

                // Determine if all items are approved or rejected
                if ($approvedQuantity == 0) {
                    $allApproved = false;
                } elseif ($approvedQuantity < $item->quantity_requested) {
                    $allApproved = false;
                    $allRejected = false;
                    $partialApproval = true;
                } else {
                    $allRejected = false;
                }
            }

            // Update the order request status
            if ($allApproved) {
                $orderRequest->status = 'Approved';
            } elseif ($allRejected) {
                $orderRequest->status = 'Rejected';
            } else {
                $orderRequest->status = 'Partial';
            }

            $orderRequest->delivery_date = $request->delivery_date;
            $orderRequest->notes = $request->notes;
            $orderRequest->approved_by = Auth::id();
            $orderRequest->approved_at = now();
            $orderRequest->save();

            // Allocate stock for approved items
            foreach ($orderRequest->items as $item) {
                if ($item->quantity_approved > 0) {
                    $headOfficeStock = HeadOfficeStock::where('gas_type_id', $item->gas_type_id)->first();

                    if ($headOfficeStock) {
                        // Deduct from available quantity
                        $headOfficeStock->available_quantity -= $item->quantity_approved;
                        $headOfficeStock->updateStatus();
                        $headOfficeStock->save();

                        // Create stock allocation
                        StockAllocation::create([
                            'head_office_stock_id' => $headOfficeStock->id,
                            'gas_type_id' => $item->gas_type_id,
                            'outlet_id' => $orderRequest->outlet_id,
                            'total_quantity' => $item->quantity_approved,
                            'allocated_quantity' => $item->quantity_approved,
                            'allocation_date' => now(),
                            'status' => 'pending'
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('admin.outlet-requests.show', $orderRequest->id)
                ->with('success', 'Order request processed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process outlet order request: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to process order request. Please try again.');
        }
    }

    /**
     * Mark a request as fulfilled.
     */
    public function markAsFulfilled($id)
    {
        // Get the order request
        $orderRequest = OutletOrderRequest::whereIn('status', ['Approved', 'Partial'])
            ->findOrFail($id);

        // Update status to Fulfilled
        $orderRequest->status = 'Fulfilled';
        $orderRequest->save();

        // Update stock allocations status
        StockAllocation::where('outlet_id', $orderRequest->outlet_id)
            ->where('allocation_date', '>=', $orderRequest->approved_at)
            ->where('status', 'pending')
            ->update(['status' => 'completed']);

        return redirect()->route('admin.outlet-requests.index')
            ->with('success', 'Order request marked as fulfilled.');
    }

    /**
     * Get statistics for dashboard.
     */
    public function getStatistics()
    {
        $statistics = [
            'pending_count' => OutletOrderRequest::where('status', 'Pending')->count(),
            'approved_count' => OutletOrderRequest::where('status', 'Approved')->count(),
            'fulfilled_count' => OutletOrderRequest::where('status', 'Fulfilled')->count(),
            'rejected_count' => OutletOrderRequest::where('status', 'Rejected')->count(),
            'recent_requests' => OutletOrderRequest::with('outlet')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(),
            'pending_by_outlet' => DB::table('outlet_order_requests')
                ->join('outlets', 'outlet_order_requests.outlet_id', '=', 'outlets.id')
                ->select('outlets.name', DB::raw('count(*) as count'))
                ->where('outlet_order_requests.status', 'Pending')
                ->groupBy('outlets.id', 'outlets.name')
                ->orderBy('count', 'desc')
                ->take(5)
                ->get()
        ];

        return response()->json($statistics);
    }
}
