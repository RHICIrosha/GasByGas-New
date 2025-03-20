<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GasRequest;
use App\Models\Outlet;
use App\Models\GasType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GasRequestController extends Controller
{
    /**
     * Display a listing of gas requests.
     */
    public function index(Request $request)
    {
        $query = GasRequest::with(['user', 'gasType', 'outlet']);

        // Search by request number
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

        // Filter by date range
        if ($request->has('from_date') && !empty($request->from_date)) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date') && !empty($request->to_date)) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Default sort by latest
        $query->orderBy('created_at', 'desc');

        // Get paginated results
        $gasRequests = $query->paginate(15);

        // Get all outlets for filter dropdown
        $outlets = Outlet::all();

        return view('admin.gas-requests.index', compact('gasRequests', 'outlets'));
    }

    /**
     * Show the form for creating a new gas request.
     */
    public function create()
    {
        $users = User::where('role', 'customer')->get();
        $gasTypes = GasType::all();
        $outlets = Outlet::all();

        return view('admin.gas-requests.create', compact('users', 'gasTypes', 'outlets'));
    }

    /**
     * Store a newly created gas request in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'gas_type_id' => 'required|exists:gas_types,id',
            'outlet_id' => 'required|exists:outlets,id',
            'quantity' => 'required|integer|min:1',
            'status' => 'required|string',
            'notes' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'expected_pickup_date' => 'nullable|date',
            'empty_cylinder_returned' => 'boolean',
            'payment_received' => 'boolean',
        ]);

        // Generate unique request number
        $validated['request_number'] = 'GR-' . date('Ymd') . '-' . rand(1000, 9999);

        $gasRequest = GasRequest::create($validated);

        return redirect()->route('admin.gas-requests.index')
            ->with('success', 'Gas request created successfully.');
    }

    /**
     * Display the specified gas request.
     */
    public function show(GasRequest $gasRequest)
    {
        $gasRequest->load(['user', 'gasType', 'outlet']);

        return view('admin.gas-requests.show', compact('gasRequest'));
    }

    /**
     * Show the form for editing the specified gas request.
     */
    public function edit(GasRequest $gasRequest)
    {
        $users = User::where('role', 'customer')->get();
        $gasTypes = GasType::all();
        $outlets = Outlet::all();

        return view('admin.gas-requests.edit', compact('gasRequest', 'users', 'gasTypes', 'outlets'));
    }

    /**
     * Update the specified gas request in storage.
     */
    public function update(Request $request, GasRequest $gasRequest)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'gas_type_id' => 'required|exists:gas_types,id',
            'outlet_id' => 'required|exists:outlets,id',
            'quantity' => 'required|integer|min:1',
            'status' => 'required|string',
            'notes' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'expected_pickup_date' => 'nullable|date',
            'actual_pickup_date' => 'nullable|date',
            'empty_cylinder_returned' => 'boolean',
            'payment_received' => 'boolean',
        ]);

        // Handle checkbox values
        $validated['empty_cylinder_returned'] = $request->has('empty_cylinder_returned');
        $validated['payment_received'] = $request->has('payment_received');

        $gasRequest->update($validated);

        return redirect()->route('admin.gas-requests.index')
            ->with('success', 'Gas request updated successfully.');
    }

    /**
     * Remove the specified gas request from storage.
     */
    public function destroy(GasRequest $gasRequest)
    {
        $gasRequest->delete();

        return redirect()->route('admin.gas-requests.index')
            ->with('success', 'Gas request deleted successfully.');
    }

    /**
     * Update payment status via AJAX.
     */
    public function updatePayment(Request $request)
    {
        $request->validate([
            'request_id' => 'required|exists:gas_requests,id',
            'status' => 'required|boolean',
        ]);

        $gasRequest = GasRequest::findOrFail($request->request_id);
        $gasRequest->payment_received = $request->status;
        $gasRequest->save();

        return response()->json([
            'success' => true,
            'message' => 'Payment status updated successfully.',
        ]);
    }

    /**
     * Update cylinder return status via AJAX.
     */
    public function updateCylinder(Request $request)
    {
        $request->validate([
            'request_id' => 'required|exists:gas_requests,id',
            'status' => 'required|boolean',
        ]);

        $gasRequest = GasRequest::findOrFail($request->request_id);
        $gasRequest->empty_cylinder_returned = $request->status;
        $gasRequest->save();

        return response()->json([
            'success' => true,
            'message' => 'Cylinder return status updated successfully.',
        ]);
    }

    /**
     * Get gas requests statistics for dashboard.
     */
    public function getStatistics()
    {
        $statistics = [
            'total' => GasRequest::count(),
            'pending' => GasRequest::where('status', 'Pending')->count(),
            'ready' => GasRequest::where('status', 'Ready for Pickup')->count(),
            'completed' => GasRequest::where('status', 'Completed')->count(),
            'monthly_requests' => $this->getMonthlyRequestsCount(),
            'top_outlets' => $this->getTopOutlets(),
        ];

        return response()->json($statistics);
    }

    /**
     * Get monthly requests count for the last 6 months.
     */
    private function getMonthlyRequestsCount()
    {
        return DB::table('gas_requests')
            ->select(DB::raw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as count'))
            ->whereRaw('created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)')
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();
    }

    /**
     * Get top 5 outlets by request count.
     */
    private function getTopOutlets()
    {
        return DB::table('gas_requests')
            ->join('outlets', 'gas_requests.outlet_id', '=', 'outlets.id')
            ->select('outlets.name', DB::raw('COUNT(*) as request_count'))
            ->groupBy('outlets.id', 'outlets.name')
            ->orderBy('request_count', 'desc')
            ->limit(5)
            ->get();
    }
}
