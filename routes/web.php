<?php

use App\Http\Controllers\Admin\HeadOfficeStockController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OutletController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Registration Routes
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Verification Routes
Route::get('/verify/{user}', [RegisterController::class, 'showVerificationForm'])->name('verification.show');
Route::post('/verify', [RegisterController::class, 'verify'])->name('verification.verify');
Route::post('/verify/resend', [RegisterController::class, 'resendCode'])->name('verification.resend');

// Dashboard Routes
Route::get('/dashboard', [DashboardController::class, 'redirectToDashboard'])->name('dashboard');
Route::get('/customer/dashboard', [DashboardController::class, 'customerDashboard'])->middleware('verified')->name('customer.dashboard');
Route::get('/business/dashboard', [DashboardController::class, 'businessDashboard'])->middleware('verified')->name('business.dashboard');
Route::get('/outlet/dashboard', [DashboardController::class, 'outletDashboard'])->name('outlet.dashboard');
Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');

// Order routes
Route::middleware(['auth'])->group(function () {
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{id}/confirmation', [OrderController::class, 'confirmation'])->name('orders.confirmation');
    Route::get('/orders/{id}/track', [OrderController::class, 'track'])->name('orders.track');
    Route::get('/orders/history', [OrderController::class, 'history'])->name('orders.history');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/find-outlets', [OutletController::class, 'findOutlets'])->name('outlets.find');
    Route::put('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
});


// Admin Controllers
Route::middleware(['auth'])->group(function () {
    // Head Office Stock Management
    Route::get('/admin/stocks', [HeadOfficeStockController::class, 'index'])->name('admin.stocks.index');
    Route::get('/admin/stocks/create', [HeadOfficeStockController::class, 'create'])->name('admin.stocks.create');
    Route::post('/admin/stocks', [HeadOfficeStockController::class, 'store'])->name('admin.stocks.store');
    Route::get('/admin/stocks/{stock}', [HeadOfficeStockController::class, 'show'])->name('admin.stocks.show');
    Route::get('/admin/stocks/{stock}/edit', [HeadOfficeStockController::class, 'edit'])->name('admin.stocks.edit');
    Route::put('/admin/stocks/{stock}', [HeadOfficeStockController::class, 'update'])->name('admin.stocks.update');
    Route::delete('/admin/stocks/{stock}', [HeadOfficeStockController::class, 'destroy'])->name('admin.stocks.destroy');

    // Additional stock actions
    Route::get('/admin/stocks/{stock}/restock', [HeadOfficeStockController::class, 'showRestockForm'])->name('admin.stocks.restock');
    Route::post('/admin/stocks/{stock}/restock', [HeadOfficeStockController::class, 'restock']);
    Route::get('/admin/stocks/{stock}/allocate', [HeadOfficeStockController::class, 'showAllocationForm'])->name('admin.stocks.allocate');
    Route::post('/admin/stocks/{stock}/allocate', [HeadOfficeStockController::class, 'allocate']);
    Route::get('/stocks/{stock}/restock', [HeadOfficeStockController::class, 'showRestockForm'])
     ->name('admin.stocks.restock');
    // Stock allocations management
    // Route::get('/admin/allocations', [StockAllocationController::class, 'index'])->name('admin.allocations.index');
    // Route::get('/admin/allocations/{allocation}', [StockAllocationController::class, 'show'])->name('admin.allocations.show');
    // Route::put('/admin/allocations/{allocation}/transit', [StockAllocationController::class, 'markAsInTransit'])->name('admin.allocations.transit');
    // Route::put('/admin/allocations/{allocation}/deliver', [StockAllocationController::class, 'markAsDelivered'])->name('admin.allocations.deliver');
    // Route::put('/admin/allocations/{allocation}/cancel', [StockAllocationController::class, 'cancel'])->name('admin.allocations.cancel');

    // // Gas type management
    // Route::get('/admin/gas-types', [GasTypeController::class, 'index'])->name('admin.gas-types.index');
    // Route::get('/admin/gas-types/create', [GasTypeController::class, 'create'])->name('admin.gas-types.create');
    // Route::post('/admin/gas-types', [GasTypeController::class, 'store'])->name('admin.gas-types.store');
    // Route::get('/admin/gas-types/{gasType}', [GasTypeController::class, 'show'])->name('admin.gas-types.show');
    // Route::get('/admin/gas-types/{gasType}/edit', [GasTypeController::class, 'edit'])->name('admin.gas-types.edit');
    // Route::put('/admin/gas-types/{gasType}', [GasTypeController::class, 'update'])->name('admin.gas-types.update');
    // Route::delete('/admin/gas-types/{gasType}', [GasTypeController::class, 'destroy'])->name('admin.gas-types.destroy');
});
