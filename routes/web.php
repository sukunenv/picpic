<?php

use App\Http\Controllers\MenuController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\MenuItemController as AdminMenuItemController;
use App\Models\Order;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ── USER ROUTES (PWA) ──────────────────────────────────
Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

Route::get('/menu', function () {
    return Inertia::render('Menu');
})->name('menu');

Route::get('/cart', function () {
    return Inertia::render('Cart');
})->name('cart');

Route::get('/profil', function () {
    return Inertia::render('Profil');
})->name('profil');

Route::get('/pesanan', function () {
    return Inertia::render('Pesanan');
})->name('pesanan');

Route::get('/order/{id}/status', function ($id) {
    $order = Order::with('orderItems.menuItem')->findOrFail($id);
    return Inertia::render('OrderStatus', ['order' => $order]);
})->name('order.status');

// API Routes untuk data
Route::prefix('api')->group(function () {
    Route::get('/menu-items', [MenuController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
});

Route::post('/cart/checkout', [CartController::class, 'store'])->name('cart.checkout');

// ── ADMIN ROUTES (KASIR) ───────────────────────────────
Route::get('/admin/login', [AuthController::class, 'login'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'authenticate'])->name('admin.authenticate');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    
    // Orders Management
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('admin.orders');
    Route::post('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.status');
    Route::post('/orders/{order}/payment', [AdminOrderController::class, 'updatePayment'])->name('admin.orders.payment');
    
    // Menu Management
    Route::get('/menu', [AdminMenuItemController::class, 'index'])->name('admin.menu');
    Route::post('/menu', [AdminMenuItemController::class, 'store'])->name('admin.menu.store');
    Route::post('/menu/{menuItem}', [AdminMenuItemController::class, 'update'])->name('admin.menu.update');
    Route::post('/menu/{menuItem}/toggle', [AdminMenuItemController::class, 'toggle'])->name('admin.menu.toggle');
    Route::delete('/menu/{menuItem}', [AdminMenuItemController::class, 'destroy'])->name('admin.menu.destroy');
});
