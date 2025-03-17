<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GasType;
use App\Models\HeadOfficeStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HeadOfficeStockController extends Controller
{
    /**
     * Display a listing of the head office stocks
     */
    public function index()
    {
        // Fetch stocks with related gas type, ordered by status
        $stocks = HeadOfficeStock::with('gasType')
            ->orderBy('status', 'desc')
            ->get()
            ->map(function ($stock) {
                // Ensure allocated quantity is calculated
                $stock->allocated_quantity = $stock->calculateHeadOfficeAllocation();
                return $stock;
            });

        // Calculate summary statistics
        $totalStock = $stocks->sum('total_quantity');
        $availableStock = $stocks->sum('available_quantity');
        $allocatedStock = $stocks->sum('allocated_quantity');

        // Count stocks by status
        $statusCounts = [
            'critical' => $stocks->where('status', 'critical')->count(),
            'low' => $stocks->where('status', 'low')->count(),
            'normal' => $stocks->where('status', 'normal')->count()
        ];

        return view('admin.stocks.index', [
            'stocks' => $stocks,
            'totalStock' => $totalStock,
            'availableStock' => $availableStock,
            'allocatedStock' => $allocatedStock,
            'criticalCount' => $statusCounts['critical'],
            'lowCount' => $statusCounts['low'],
            'normalCount' => $statusCounts['normal']
        ]);
    }

    /**
     * Show the form for creating a new stock
     */
    public function create()
    {
        // Get all active gas types
        $gasTypes = GasType::where('is_active', true)->get();

        // Get existing stocks to show current quantities
        $existingStocks = HeadOfficeStock::with('gasType')->get()->keyBy('gas_type_id');

        // Change from availableGasTypes to gasTypes
        return view('admin.stocks.create', [
            'gasTypes' => $gasTypes,
            'existingStocks' => $existingStocks
        ]);
    }
    /**
     * Store a newly created stock
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'gas_type_id' => 'required|exists:gas_types,id',
            'total_quantity' => 'required|numeric|min:0.01',
            'reserve_for_head_office' => 'nullable|numeric|min:0|max:' . $request->input('total_quantity'),
            'minimum_stock_level' => 'required|numeric|min:0.01',
        ]);

        return DB::transaction(function () use ($validated) {
            // Calculate reserved/allocated quantity
            $reserveForHeadOffice = $validated['reserve_for_head_office'] ?? 0;
            $availableQuantity = $validated['total_quantity'] - $reserveForHeadOffice;

            // Find existing stock or prepare new stock
            $stock = HeadOfficeStock::firstOrNew(
                ['gas_type_id' => $validated['gas_type_id']],
                [
                    'total_quantity' => 0,
                    'available_quantity' => 0,
                    'allocated_quantity' => 0,
                    'minimum_stock_level' => $validated['minimum_stock_level']
                ]
            );

            // Update quantities explicitly
            $stock->total_quantity += $validated['total_quantity'];
            $stock->available_quantity += $availableQuantity;
            $stock->allocated_quantity += $reserveForHeadOffice;
            $stock->minimum_stock_level = $validated['minimum_stock_level'];
            $stock->last_restock_date = now();

            // Determine stock status
            $stock->status = match(true) {
                $stock->available_quantity <= $stock->minimum_stock_level / 2 => 'critical',
                $stock->available_quantity <= $stock->minimum_stock_level => 'low',
                default => 'normal'
            };

            // Save the stock with explicit allocated_quantity
            $stock->save();

            return redirect()->route('admin.stocks.index')
                ->with('success', $stock->wasRecentlyCreated
                    ? 'New stock created successfully.'
                    : 'Existing stock updated successfully.');
        });
    }
    /**
     * Show stock details
     */
    public function show(HeadOfficeStock $stock)
    {
        // Load related gas type
        $stock->load('gasType');

        // Calculate head office allocation
        $stock->allocated_quantity = $stock->calculateHeadOfficeAllocation();

        return view('admin.stocks.show', compact('stock'));
    }

    /**
     * Edit stock
     */
    public function edit(HeadOfficeStock $stock)
    {
        // Load related gas type
        $stock->load('gasType');

        return view('admin.stocks.edit', compact('stock'));
    }

    /**
     * Update stock
     */
    public function update(Request $request, HeadOfficeStock $stock)
    {
        // Validate input
        $validated = $request->validate([
            'total_quantity' => 'required|numeric|min:' . $stock->allocated_quantity,
            'head_office_reserve' => 'nullable|numeric|min:0|max:' . $request->input('total_quantity'),
            'minimum_stock_level' => 'required|numeric|min:0.01',
        ]);

        return DB::transaction(function () use ($validated, $stock) {
            // Calculate available and reserved quantities
            $headOfficeReserve = $validated['head_office_reserve'] ?? $stock->allocated_quantity;
            $availableQuantity = $validated['total_quantity'] - $headOfficeReserve;

            // Update stock details
            $stock->total_quantity = $validated['total_quantity'];
            $stock->available_quantity = $availableQuantity;
            $stock->allocated_quantity = $headOfficeReserve;
            $stock->minimum_stock_level = $validated['minimum_stock_level'];

            // Update status
            $stock->status = match(true) {
                $availableQuantity <= $validated['minimum_stock_level'] / 2 => 'critical',
                $availableQuantity <= $validated['minimum_stock_level'] => 'low',
                default => 'normal'
            };

            // Save the updated stock
            $stock->save();

            // Log the update
            Log::info('Head Office Stock Updated', [
                'stock_id' => $stock->id,
                'total_quantity' => $stock->total_quantity,
                'available_quantity' => $stock->available_quantity,
                'allocated_quantity' => $stock->allocated_quantity
            ]);

            return redirect()->route('admin.stocks.index')
                ->with('success', 'Stock updated successfully.');
        });
    }

    /**
     * Restock head office stock
     */
    public function restock(Request $request, HeadOfficeStock $stock)
    {
        $validated = $request->validate([
            'quantity' => 'required|numeric|min:0.01',
            'reserve_for_head_office' => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function () use ($validated, $stock) {
            // Calculate current reserved quantity
            $currentReserved = $stock->allocated_quantity;

            // Determine new reservation
            $newReserved = $validated['reserve_for_head_office'] ?? $currentReserved;

            // Calculate available and reserved quantities
            $quantityToReserve = max(0, $newReserved);
            $addToAvailable = $validated['quantity'] - $quantityToReserve;

            // Update stock quantities with explicit allocated_quantity update
            $stock->total_quantity += $validated['quantity'];
            $stock->available_quantity += $addToAvailable;
            $stock->allocated_quantity += $quantityToReserve;
            $stock->last_restock_date = now();

            // Update status
            $stock->updateStatus();

            // Save with explicit allocated_quantity
            $stock->save();

            return redirect()->route('admin.stocks.index')
                ->with('success', 'Stock restocked successfully.');
        });
    }

    /**
     * Delete stock
     */
    public function destroy(HeadOfficeStock $stock)
    {
        try {
            // Delete the stock
            $stock->delete();

            return redirect()->route('admin.head-office-stocks.index')
                ->with('success', 'Stock deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting head office stock: ' . $e->getMessage());

            return back()->with('error', 'Error deleting stock. Please try again.');
        }
    }
    /**
 * Show the form for restocking a specific stock
 */
    public function showRestockForm(HeadOfficeStock $stock)
    {
        // Load related gas type
        $stock->load('gasType');

        // Calculate current reserved quantity
        $currentReserved = $stock->total_quantity - $stock->available_quantity;

        return view('admin.stocks.restock', [
            'stock' => $stock,
            'currentReserved' => $currentReserved
        ]);
    }
}
