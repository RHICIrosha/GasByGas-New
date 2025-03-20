<?php

use App\Http\Controllers\Admin\GasRequestController;
use App\Http\Controllers\Admin\HeadOfficeStockController;
use App\Http\Controllers\Admin\OutletOrderRequestController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Outlet\OutletOrderRequestController as OutletOutletOrderRequestController;
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

});
// Admin Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    Route::controller(OutletController::class)->group(function () {
        Route::get('/outlets', 'index')->name('outlets.index');
        Route::get('/outlets/create', 'create')->name('outlets.create');
        Route::post('/outlets', [OutletController::class, 'store'])->name('outlets.store');
        Route::get('/outlets/{outlet}', 'show')->name('outlets.show');
        Route::get('/outlets/{outlet}/edit', 'edit')->name('outlets.edit');
        Route::put('/outlets/{outlet}', 'update')->name('outlets.update');
        Route::delete('/outlets/{outlet}', [OutletController::class, 'destroy'])->name('outlets.destroy');
        Route::put('/outlets/{outlet}/assign-manager', 'assignManager')->name('outlets.assign-manager');

          // Display all gas requests
    Route::get('/gas-requests', [GasRequestController::class, 'index'])->name('gas-requests.index');

    // Show create gas request form
    Route::get('/gas-requests/create', [GasRequestController::class, 'create'])->name('gas-requests.create');

    // Store new gas request
    Route::post('/gas-requests', [GasRequestController::class, 'store'])->name('gas-requests.store');

    // Show specific gas request details
    Route::get('/gas-requests/{gasRequest}', [GasRequestController::class, 'show'])->name('gas-requests.show');

    // Show edit gas request form
    Route::get('/gas-requests/{gasRequest}/edit', [GasRequestController::class, 'edit'])->name('gas-requests.edit');

    // Update gas request
    Route::put('/gas-requests/{gasRequest}', [GasRequestController::class, 'update'])->name('gas-requests.update');

    // Delete gas request
    Route::delete('/gas-requests/{gasRequest}', [GasRequestController::class, 'destroy'])->name('gas-requests.destroy');

    // Update payment status via AJAX
    Route::post('/gas-requests/update-payment', [GasRequestController::class, 'updatePayment'])->name('gas-requests.update-payment');

    // Update cylinder return status via AJAX
    Route::post('/gas-requests/update-cylinder', [GasRequestController::class, 'updateCylinder'])->name('gas-requests.update-cylinder');


    });
});
// In your routes file
Route::post('/outlets/{outlet}/delete', [OutletController::class, 'destroy'])->name('outlets.destroy');

//Outlet routes
Route::middleware(['auth', 'role:outlet_manager'])->prefix('outlet')->name('outlet.')->group(function () {
    // Order Requests Routes
    Route::get('/order-requests/index', [OutletOutletOrderRequestController::class, 'index'])->name('order-requests.index');
    Route::get('/order-requests/create', [OutletOutletOrderRequestController::class, 'create'])->name('order-requests.create');
    Route::post('/order-requests', [OutletOutletOrderRequestController::class, 'store'])->name('order-requests.store');
    Route::get('/order-requests/{id}', [OutletOutletOrderRequestController::class, 'show'])->name('order-requests.show');
    Route::get('/order-requests/{id}/edit', [OutletOutletOrderRequestController::class, 'edit'])->name('order-requests.edit');
    Route::put('/order-requests/{id}', [OutletOutletOrderRequestController::class, 'update'])->name('order-requests.update');
    Route::put('/order-requests/{id}/cancel', [OutletOutletOrderRequestController::class, 'cancel'])->name('order-requests.cancel');


});

