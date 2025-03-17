<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class OutletController extends Controller
{
    public function findOutlets()
    {
        $outlets = Outlet::all();
        foreach ($outlets as $outlet) {
            if (!isset($outlet->latitude) || !isset($outlet->longitude)) {
                $outlet->latitude = null;
                $outlet->longitude = null;
            }
        }

        return view('customer.find_outlets', compact('outlets'));
    }
    public function index()
    {
        // Fetch outlets with their managers
        $outlets = DB::table('outlets')
            ->leftJoin('users', 'outlets.manager_id', '=', 'users.id')
            ->select(
                'outlets.*',
                'users.id as user_id',
                'users.name as manager_name',
                'users.email as manager_email'
            )
            ->get();

        // Get total count
        $stats = [
            'total' => DB::table('outlets')->count()
        ];

        // Get available managers - using 'type' instead of 'role'
        $managers = DB::table('users')
            ->where('user_type', 'outlet_manager')
            ->get();

        return view('admin.outlets.index', compact('outlets', 'stats', 'managers'));
    }

    public function create()
    {
        // Get outlet managers for the form dropdown
        $managers = DB::table('users')
            ->where('user_type', 'outlet_manager')
            ->get();

        return view('admin.outlets.create', compact('managers'));
    }

    public function store(Request $request)
    {
        // Validate outlet data
        $validated = $request->validate([
            'code' => 'required|string|unique:outlets,code',
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'contact_number' => 'required|string|max:20',
            'manager_id' => 'nullable|exists:users,id',
        ]);

        // Set boolean fields based on checkbox values
        $validated['has_stock'] = $request->has('has_stock') ? 1 : 0;
        $validated['is_accepting_orders'] = $request->has('is_accepting_orders') ? 1 : 0;

        try {
            // Create outlet record
            DB::table('outlets')->insert($validated);

            return redirect()->route('admin.outlets.index')
                ->with('success', 'Outlet created successfully.');
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Failed to create outlet: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create outlet: ' . $e->getMessage());
        }
    }
public function edit($id)
{
    $outlet = DB::table('outlets')
        ->leftJoin('users', 'outlets.manager_id', '=', 'users.id')
        ->select(
            'outlets.*',
            'users.name as manager_name',
            'users.email as manager_email'
        )
        ->where('outlets.id', $id)
        ->first();

    if (!$outlet) {
        return redirect()->route('admin.outlets.index')
            ->with('error', 'Outlet not found.');
    }

    $managers = DB::table('users')
        ->where('user_type', 'outlet_manager')
        ->get();

    return view('admin.outlets.edit', compact('outlet', 'managers'));
}

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:outlets,code,'.$id,
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'contact_number' => 'required|string|max:20',
            'manager_id' => 'nullable|exists:users,id',
            'has_stock' => 'boolean',
            'is_accepting_orders' => 'boolean'
        ]);

        // Set default values for boolean fields if not provided
        $validated['has_stock'] = $request->has('has_stock') ? 1 : 0;
        $validated['is_accepting_orders'] = $request->has('is_accepting_orders') ? 1 : 0;

        try {
            DB::table('outlets')->where('id', $id)->update($validated);

            return redirect()->route('admin.outlets.index')
                ->with('success', 'Outlet updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update outlet: ' . $e->getMessage());
        }
    }
    public function assignManager(Request $request, $id)
    {
        // Validate request
        $validated = $request->validate([
            'manager_id' => 'nullable|exists:users,id',
        ]);

        // Update outlet manager
        DB::table('outlets')->where('id', $id)->update([
            'manager_id' => $validated['manager_id']
        ]);

        return redirect()->route('admin.outlets.index')
            ->with('success', 'Outlet manager updated successfully.');
    }

    public function destroy($id)
    {
        DB::table('outlets')->where('id', $id)->delete();

        return redirect()->route('admin.outlets.index')
            ->with('success', 'Outlet deleted successfully.');
    }
}
