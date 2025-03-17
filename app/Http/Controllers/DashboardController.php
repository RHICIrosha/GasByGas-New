<?php
namespace App\Http\Controllers;

use App\Models\GasInventory;
use App\Models\GasRequest;
use App\Models\GasType;
use App\Models\Outlet;
use App\Models\Token;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified')->except(['index', 'redirectToDashboard']);
    }

    /**
     * Redirect users to appropriate dashboard based on role
     */
    public function redirectToDashboard()
    {
        $user = Auth::user();

        // Check user_type directly instead of using helper methods
        if ($user->user_type === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->user_type === 'outlet_manager') {
            return redirect()->route('outlet.dashboard');
        } elseif ($user->user_type === 'business') {
            if (!$user->is_verified) {
                return redirect()->route('verification.show', ['user' => $user->id]);
            }
            return redirect()->route('business.dashboard');
        } else {
            if (!$user->is_verified) {
                return redirect()->route('verification.show', ['user' => $user->id]);
            }
            return redirect()->route('customer.dashboard');
        }
    }

    /**
     * Display customer dashboard
     */
    public function customerDashboard()
    {
        $user = Auth::user();

        // Dashboard stats
        $totalOrders = GasRequest::where('user_id', $user->id)->count();
        $pendingDeliveries = GasRequest::where('user_id', $user->id)
                                ->whereIn('status', ['Pending', 'Confirmed'])
                                ->count();
        $completedOrders = GasRequest::where('user_id', $user->id)
                                ->where('status', 'Completed')
                                ->count();
        $activeTokens = Token::where('user_id', $user->id)
                            ->where('is_active', true)
                            ->count();

        // Gas types
        $gasTypes = GasType::all();

        // Recent orders
        $recentOrders = GasRequest::with(['gasType', 'outlet'])
                        ->where('user_id', $user->id)
                        ->orderBy('created_at', 'desc')
                        ->take(3)
                        ->get();

        // Nearest outlets
        // In a real app, you'd implement some sort of geolocation-based query
        // For now, just get outlets with stock and accepting orders
        $nearestOutlets = Outlet::where('has_stock', true)
                            ->where('is_accepting_orders', true)
                            ->take(3)
                            ->get();

        return view('customer.dashboard', compact(
            'totalOrders',
            'pendingDeliveries',
            'completedOrders',
            'activeTokens',
            'gasTypes',
            'recentOrders',
            'nearestOutlets'
        ));
    }


    /**
     * Display business customer dashboard
     */
    public function businessDashboard()
    {
        $user = Auth::user();

        // Dashboard stats
        $totalOrders = GasRequest::where('user_id', $user->id)->count();
        $pendingDeliveries = GasRequest::where('user_id', $user->id)
                                ->whereIn('status', ['Pending', 'Confirmed'])
                                ->count();
        $completedOrders = GasRequest::where('user_id', $user->id)
                                ->where('status', 'Completed')
                                ->count();
        $activeTokens = Token::where('user_id', $user->id)
                            ->where('is_active', true)
                            ->count();

        // Gas types
        $gasTypes = GasType::all();

        // Recent orders
        $recentOrders = GasRequest::with(['gasType', 'outlet'])
                        ->where('user_id', $user->id)
                        ->orderBy('created_at', 'desc')
                        ->take(3)
                        ->get();

        // Nearest outlets
        // In a real app, you'd implement some sort of geolocation-based query
        // For now, just get outlets with stock and accepting orders
        $nearestOutlets = Outlet::where('has_stock', true)
                            ->where('is_accepting_orders', true)
                            ->take(3)
                            ->get();
        return view('business.dashboard', compact(
            'totalOrders',
            'pendingDeliveries',
            'completedOrders',
            'activeTokens',
            'gasTypes',
            'recentOrders',
            'nearestOutlets'
        ));
    }

    /**
     * Display outlet manager dashboard
     */
    public function outletDashboard()
    {
        $user = Auth::user();
        $outlet = Outlet::first();
        if (!$outlet) {
            return redirect()->route('home')->with('error', 'No outlets have been configured in the system.');
        }
        $pendingTokens = Token::with(['user', 'gasRequest.gasType'])
        ->where('outlet_id', $outlet->id)
        ->where('is_active', true)
        ->where('status', 'Valid')
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();
        $pendingTokensCount = Token::where('outlet_id', $outlet->id)
        ->where('is_active', true)
        ->where('status', 'Valid')
        ->count();
        $todayPickupsCount = Token::where('outlet_id', $outlet->id)
        ->whereDate('updated_at', Carbon::today())
        ->where('status', 'Used')
        ->count();
        $inventory = GasInventory::with('gasType')
        ->where('outlet_id', $outlet->id)
        ->get();
        $totalStock = $inventory->sum('quantity');

    $emptyReturnsCount = GasRequest::where('outlet_id', $outlet->id)
                        ->where('status', 'Completed')
                        ->whereBetween('updated_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                        ->count();

                        return view('outlet.dashboard', compact(
                            'outlet',
                            'pendingTokens',
                            'pendingTokensCount',
                            'todayPickupsCount',
                            'inventory',
                            'totalStock',
                            'emptyReturnsCount'
                        ));
    }

    /**
     * Display admin dashboard
     */
    public function adminDashboard()
    {
        return view('admin.dashboard');
    }
}
