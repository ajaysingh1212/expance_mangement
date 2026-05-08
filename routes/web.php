<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Auth\AuthController;

use Illuminate\Support\Facades\Route;

// Auth Routes (not under admin prefix)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Redirect root to admin dashboard
Route::get('/', fn() => redirect()->route('admin.dashboard'));

// Admin Routes (all under /admin prefix with auth middleware)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'active.user'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Routes (available to all authenticated users)
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::post('/avatar', [ProfileController::class, 'updateAvatar'])->name('avatar');
        Route::post('/cover', [ProfileController::class, 'updateCover'])->name('cover');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
        Route::get('/{user}', [ProfileController::class, 'show'])->name('show');
    });

    // User Management (gate-protected)
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index')->middleware('can:users.index');
        Route::get('/create', [UserController::class, 'create'])->name('create')->middleware('can:users.create');
        Route::post('/', [UserController::class, 'store'])->name('store')->middleware('can:users.create');
        Route::get('/{user}', [UserController::class, 'show'])->name('show')->middleware('can:users.show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit')->middleware('can:users.edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update')->middleware('can:users.edit');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy')->middleware('can:users.delete');
        Route::patch('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status')->middleware('can:users.edit');
    });

    // Role Management (gate-protected)
    Route::prefix('roles')->name('roles.')->middleware('can:roles.index')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/create', [RoleController::class, 'create'])->name('create')->middleware('can:roles.create');
        Route::post('/', [RoleController::class, 'store'])->name('store')->middleware('can:roles.create');
        Route::get('/{role}', [RoleController::class, 'show'])->name('show');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit')->middleware('can:roles.edit');
        Route::put('/{role}', [RoleController::class, 'update'])->name('update')->middleware('can:roles.edit');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy')->middleware('can:roles.delete');
    });

    // Permission Management (gate-protected)
    Route::prefix('permissions')->name('permissions.')->middleware('can:permissions.index')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('index');
        Route::get('/create', [PermissionController::class, 'create'])->name('create')->middleware('can:permissions.create');
        Route::post('/', [PermissionController::class, 'store'])->name('store')->middleware('can:permissions.create');
        Route::get('/{permission}', [PermissionController::class, 'show'])->name('show');
        Route::get('/{permission}/edit', [PermissionController::class, 'edit'])->name('edit')->middleware('can:permissions.edit');
        Route::put('/{permission}', [PermissionController::class, 'update'])->name('update')->middleware('can:permissions.edit');
        Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('destroy')->middleware('can:permissions.delete');
    });

    // Items Module (gate-protected, RBAC demo)
    Route::prefix('items')->name('items.')->group(function () {
        Route::get('/', [ItemController::class, 'index'])->name('index')->middleware('can:items.index');
        Route::get('/create', [ItemController::class, 'create'])->name('create')->middleware('can:items.create');
        Route::post('/', [ItemController::class, 'store'])->name('store')->middleware('can:items.create');
        Route::get('/{item}', [ItemController::class, 'show'])->name('show')->middleware('can:items.show');
        Route::get('/{item}/edit', [ItemController::class, 'edit'])->name('edit')->middleware('can:items.edit');
        Route::put('/{item}', [ItemController::class, 'update'])->name('update')->middleware('can:items.edit');
        Route::delete('/{item}', [ItemController::class, 'destroy'])->name('destroy')->middleware('can:items.delete');
    });

    // Site Settings (super-admin/admin only)
    Route::prefix('settings')->name('settings.')->middleware('can:settings.index')->group(function () {
        Route::get('/', [SiteSettingController::class, 'index'])->name('index');
        Route::post('/', [SiteSettingController::class, 'update'])->name('update')->middleware('can:settings.edit');
        Route::post('/logo', [SiteSettingController::class, 'uploadLogo'])->name('logo')->middleware('can:settings.edit');
    });

    // Activity Logs (admin+ only)
    Route::get('/activity', [ActivityLogController::class, 'index'])->name('activity.index')->middleware('can:activity.index');
    Route::post('/activity/clear', [ActivityLogController::class, 'clear'])
    ->name('activity.clear')
    ->middleware('can:activity.index');
    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::patch('/{notification}/read', [NotificationController::class, 'markRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllRead'])->name('read-all');
    });
});
