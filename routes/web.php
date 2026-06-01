<?php

use App\Http\Controllers\{AuthController, HomeController, AdminController, OrderController, ProfileController, PasswordResetController};
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/contact', [HomeController::class, 'storeContact'])->name('contact.store');

Route::middleware('guest')->group(function () {
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login']);
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Password reset via email code
    Route::get('/forgot-password',          [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password',         [PasswordResetController::class, 'sendCode'])->name('password.email');
    Route::get('/forgot-password/verify',   [PasswordResetController::class, 'showCodeForm'])->name('password.code-form');
    Route::post('/forgot-password/verify',  [PasswordResetController::class, 'verifyCode'])->name('password.verify-code');
    Route::get('/forgot-password/reset',    [PasswordResetController::class, 'showResetForm'])->name('password.reset-form');
    Route::post('/forgot-password/reset',   [PasswordResetController::class, 'updatePassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/cart', fn() => view('user.cart'))->name('cart');

    Route::post('/orders',                 [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders',                  [OrderController::class, 'index'])->name('orders.index');
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    Route::get('/profile',                       [ProfileController::class, 'show'])->name('profile');
    Route::patch('/profile',                     [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password',            [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::patch('/profile/photo',               [ProfileController::class, 'updatePhoto'])->name('profile.photo');
    Route::patch('/profile/address',             [ProfileController::class, 'updateAddress'])->name('profile.address');
    Route::patch('/profile/preferences',         [ProfileController::class, 'updatePreferences'])->name('profile.preferences');
});

Route::middleware(['auth', 'role:admin,staff'])->prefix('dashboard')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/orders',                          [AdminController::class, 'orders'])->name('staff.orders');
    Route::get('/orders/{order}',                  [AdminController::class, 'showOrder'])->name('staff.orders.show');
    Route::patch('/orders/{order}/status',         [AdminController::class, 'updateOrderStatus'])->name('staff.orders.status');
    Route::get('/menu',                            [AdminController::class, 'menuIndex'])->name('staff.menu');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Users
    Route::get('/users',                 [AdminController::class, 'users'])->name('users');
    Route::post('/users',                [AdminController::class, 'storeUser'])->name('users.store');
    Route::patch('/users/{user}',        [AdminController::class, 'updateUser'])->name('users.update');
    Route::patch('/users/{user}/role',   [AdminController::class, 'updateUserRole'])->name('users.role');
    Route::delete('/users/{user}',       [AdminController::class, 'destroyUser'])->name('users.destroy');

    // Menu
    Route::get('/menu',                  [AdminController::class, 'menuIndex'])->name('menu');
    Route::get('/menu/create',           [AdminController::class, 'menuCreate'])->name('menu.create');
    Route::post('/menu',                 [AdminController::class, 'menuStore'])->name('menu.store');
    Route::get('/menu/{item}/edit',      [AdminController::class, 'menuEdit'])->name('menu.edit');
    Route::put('/menu/{item}',           [AdminController::class, 'menuUpdate'])->name('menu.update');
    Route::patch('/menu/{item}/toggle',  [AdminController::class, 'menuToggle'])->name('menu.toggle');
    Route::patch('/menu/{item}/feature', [AdminController::class, 'menuFeature'])->name('menu.feature');
    Route::delete('/menu/{item}',        [AdminController::class, 'menuDestroy'])->name('menu.destroy');

    // Orders
    Route::get('/orders',                [AdminController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}',        [AdminController::class, 'showOrder'])->name('orders.show');
    Route::patch('/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.status');

    // Contacts
    Route::get('/contacts',                       [AdminController::class, 'contacts'])->name('contacts');
    Route::patch('/contacts/{contact}/mark-read', [AdminController::class, 'markContactRead'])->name('contacts.mark-read');
    Route::post('/contacts/read-all',             [AdminController::class, 'markAllContactsRead'])->name('contacts.read-all');
    Route::delete('/contacts/{contact}',          [AdminController::class, 'destroyContact'])->name('contacts.destroy');

    // Analytics
    Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');
});
