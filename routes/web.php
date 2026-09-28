<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Cashier\PaymentController;
use App\Http\Controllers\Cooker\KitchenController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Supervisor\ReportController;
use App\Http\Controllers\Waiter\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Public customer-facing menu page (scannable via QR code) - no auth required
Route::get('/menu', [MenuController::class, 'publicIndex'])->name('menu.public');
Route::get('/menu/qrcode', [MenuController::class, 'qrCode'])->name('menu.qrcode')->middleware('auth');

// Role-based dashboard (auto-redirects based on user role)
Route::middleware('auth')->get('/dashboard', DashboardController::class)->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications (AJAX-polled bell icon)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
});

// Waiter routes
Route::middleware(['auth', 'role:waiter'])->prefix('waiter')->name('waiter.')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/deliver', [OrderController::class, 'markDelivered'])->name('orders.deliver');
    Route::post('/orders/{order}/reassign', [OrderController::class, 'reassign'])->name('orders.reassign');
});

// Cooker routes
Route::middleware(['auth', 'role:cooker'])->prefix('cooker')->name('cooker.')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/kitchen', [KitchenController::class, 'index'])->name('kitchen');
    Route::get('/orders/{order}', [KitchenController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/accept', [KitchenController::class, 'accept'])->name('orders.accept');
    Route::post('/orders/{order}/start', [KitchenController::class, 'startCooking'])->name('orders.start');
    Route::post('/orders/{order}/ready', [KitchenController::class, 'markReady'])->name('orders.ready');
});

// Cashier routes
Route::middleware(['auth', 'role:cashier'])->prefix('cashier')->name('cashier.')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{order}/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments/{order}', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');
});

// Supervisor routes
Route::middleware(['auth', 'role:supervisor'])->prefix('supervisor')->name('supervisor.')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/orders', [ReportController::class, 'orders'])->name('orders');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
});

/**
 * Admin-only routes (full system control: users, settings, tables).
 * Supervisor is NOT granted access here - these are higher-privilege operations.
 */
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Users (admin only - supervisors cannot manage users)
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

    // Restaurant tables (admin only)
    Route::resource('tables', TableController::class);
});

/**
 * Menu management routes - shared between Admin and Supervisor.
 * Both roles can add/edit/remove menu items and categories.
 *
 * The URL prefix is /admin/... for backward compatibility with existing views,
 * but both admin and supervisor users can access these routes.
 */
Route::middleware(['auth', 'role:admin,supervisor'])->prefix('admin')->name('admin.')->group(function () {
    // Categories (full CRUD)
    Route::resource('categories', CategoryController::class);

    // Menu items (full CRUD + toggle availability)
    Route::resource('menu-items', MenuItemController::class);
    Route::post('/menu-items/{menuItem}/toggle-availability', [MenuItemController::class, 'toggleAvailability'])->name('menu-items.toggle-availability');
});

require __DIR__.'/auth.php';
