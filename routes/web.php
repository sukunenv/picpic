<?php

use App\Http\Controllers\MenuController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\MenuItemController as AdminMenuItemController;
use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Models\Order;
use App\Models\Banner;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ── HEALTHCHECK ROUTE ─────────────────────────────────────
// Selalu return 200 — dipakai Railway healthcheck,
// tidak terpengaruh maintenance mode.
Route::get('/health', function () {
    return response('OK', 200);
});

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

Route::get('/order/{id}/receipt', [OrderController::class, 'receipt'])->name('order.receipt');

// API Routes untuk data
Route::prefix('api')->group(function () {
    Route::get('/menu-items', [MenuController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::get('/banners', function () {
        return response()->json([
            'data' => Banner::active()->orderBy('order')->get()
        ]);
    });
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
    Route::get('/kasir', [AdminOrderController::class, 'kasir'])->name('admin.kasir');
    Route::post('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.status');
    Route::post('/orders/{order}/payment', [AdminOrderController::class, 'updatePayment'])->name('admin.orders.payment');
    Route::post('/orders/{order}/quick-confirm', [AdminOrderController::class, 'quickConfirm'])->name('admin.orders.quickConfirm');
    
    // Menu Management
    Route::get('/menu', [AdminMenuItemController::class, 'index'])->name('admin.menu');
    Route::post('/menu', [AdminMenuItemController::class, 'store'])->name('admin.menu.store');
    Route::post('/menu/{menuItem}', [AdminMenuItemController::class, 'update'])->name('admin.menu.update');
    Route::post('/menu/{menuItem}/toggle', [AdminMenuItemController::class, 'toggle'])->name('admin.menu.toggle');
    Route::delete('/menu/{menuItem}', [AdminMenuItemController::class, 'destroy'])->name('admin.menu.destroy');
    
    // Banners Management
    Route::get('/banners', [AdminBannerController::class, 'index'])->name('admin.banners');
    Route::post('/banners', [AdminBannerController::class, 'store'])->name('admin.banners.store');
    Route::post('/banners/{banner}', [AdminBannerController::class, 'update'])->name('admin.banners.update');
    Route::post('/banners/{banner}/toggle', [AdminBannerController::class, 'toggle'])->name('admin.banners.toggle');
    Route::delete('/banners/{banner}', [AdminBannerController::class, 'destroy'])->name('admin.banners.destroy');
});
