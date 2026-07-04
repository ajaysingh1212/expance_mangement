<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PinController;
use Illuminate\Support\Facades\Route;

// ── Auth ──────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register'])->name('register.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
Route::get('/', fn() => redirect()->route('admin.dashboard'));

// pin route('routeName', array)

Route::middleware('auth')->group(function () {
    Route::get('/pin',          [PinController::class, 'showPin'])->name('pin.show');
    Route::post('/pin/verify',  [PinController::class, 'verifyPin'])->name('pin.verify');
    Route::post('/pin/switch',  [PinController::class, 'switchAccount'])->name('pin.switch');
});
Route::middleware(['auth', 'active.user'])->group(function () {
    Route::post('/admin/profile/pin/setup',   [PinController::class, 'setupPin'])->name('profile.pin.setup');
    Route::post('/admin/profile/pin/disable', [PinController::class, 'disablePin'])->name('profile.pin.disable');
});

// ── Admin ─────────────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'active.user','require.pin'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/',          [ProfileController::class, 'index'])->name('index');
        Route::get('/edit',      [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update',    [ProfileController::class, 'update'])->name('update');
        Route::post('/avatar',   [ProfileController::class, 'updateAvatar'])->name('avatar');
        Route::post('/cover',    [ProfileController::class, 'updateCover'])->name('cover');
        Route::put('/password',  [ProfileController::class, 'updatePassword'])->name('password');
        Route::get('/{user}',    [ProfileController::class, 'show'])->name('show');
    });

    // Users
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/',                    [UserController::class, 'index'])->name('index')->middleware('can:users.index');
        Route::get('/create',              [UserController::class, 'create'])->name('create')->middleware('can:users.create');
        Route::post('/',                   [UserController::class, 'store'])->name('store')->middleware('can:users.create');
        Route::get('/{user}',              [UserController::class, 'show'])->name('show')->middleware('can:users.show');
        Route::get('/{user}/edit',         [UserController::class, 'edit'])->name('edit')->middleware('can:users.edit');
        Route::put('/{user}',              [UserController::class, 'update'])->name('update')->middleware('can:users.edit');
        Route::delete('/{user}',           [UserController::class, 'destroy'])->name('destroy')->middleware('can:users.delete');
        Route::patch('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status')->middleware('can:users.edit');
    });

    // Roles
    Route::prefix('roles')->name('roles.')->middleware('can:roles.index')->group(function () {
        Route::get('/',           [RoleController::class, 'index'])->name('index');
        Route::get('/create',     [RoleController::class, 'create'])->name('create')->middleware('can:roles.create');
        Route::post('/',          [RoleController::class, 'store'])->name('store')->middleware('can:roles.create');
        Route::get('/{role}',     [RoleController::class, 'show'])->name('show');
        Route::get('/{role}/edit',[RoleController::class, 'edit'])->name('edit')->middleware('can:roles.edit');
        Route::put('/{role}',     [RoleController::class, 'update'])->name('update')->middleware('can:roles.edit');
        Route::delete('/{role}',  [RoleController::class, 'destroy'])->name('destroy')->middleware('can:roles.delete');
    });

    // Permissions
    Route::prefix('permissions')->name('permissions.')->middleware('can:permissions.index')->group(function () {
        Route::get('/',                [PermissionController::class, 'index'])->name('index');
        Route::get('/create',          [PermissionController::class, 'create'])->name('create')->middleware('can:permissions.create');
        Route::post('/',               [PermissionController::class, 'store'])->name('store')->middleware('can:permissions.create');
        Route::get('/{permission}',    [PermissionController::class, 'show'])->name('show');
        Route::get('/{permission}/edit',[PermissionController::class, 'edit'])->name('edit')->middleware('can:permissions.edit');
        Route::put('/{permission}',    [PermissionController::class, 'update'])->name('update')->middleware('can:permissions.edit');
        Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('destroy')->middleware('can:permissions.delete');
    });

    // Finance
    Route::prefix('finance')->name('finance.')->group(function () {

        // Ledgers
        Route::get('/ledgers',  [FinanceController::class, 'ledgers'])->name('ledgers.index')->middleware('can:finance.ledgers.index');
        Route::post('/ledgers', [FinanceController::class, 'storeLedger'])->name('ledgers.store')->middleware('can:finance.ledgers.create');
        Route::get('/ledgers/{ledger}', [FinanceController::class, 'showLedger'])->name('ledgers.show')->middleware('can:finance.ledgers.show');
        Route::put('/ledgers/{ledger}', [FinanceController::class, 'updateLedger'])->name('ledgers.update')->middleware('can:finance.ledgers.edit');
        Route::delete('/ledgers/{ledger}', [FinanceController::class, 'destroyLedger'])->name('ledgers.destroy')->middleware('can:finance.ledgers.delete');

        // Bank Accounts
        Route::get('/bank-accounts',   [FinanceController::class, 'bankAccounts'])->name('bank-accounts.index')->middleware('can:finance.bank.index');
        Route::post('/bank-accounts',  [FinanceController::class, 'storeBankAccount'])->name('bank-accounts.store')->middleware('can:finance.bank.create');
        Route::get('/bank-accounts/{bankAccount}', [FinanceController::class, 'showBankAccount'])->name('bank-accounts.show')->middleware('can:finance.bank.show');
        Route::put('/bank-accounts/{bankAccount}', [FinanceController::class, 'updateBankAccount'])->name('bank-accounts.update')->middleware('can:finance.bank.edit');
        Route::delete('/bank-accounts/{bankAccount}', [FinanceController::class, 'destroyBankAccount'])->name('bank-accounts.destroy')->middleware('can:finance.bank.delete');

        // Statement
        Route::get('/statement',  [FinanceController::class, 'statement'])->name('statement.index')->middleware('can:finance.bank.index');

        // ── NEW: Manual bank entry & reconciliation ──────────────────────
        Route::post('/bank-accounts/manual-entry',           [FinanceController::class, 'storeManualBankEntry'])->name('bank-accounts.manual-entry')->middleware('can:finance.approve');
        Route::patch('/transactions/{transaction}/reconcile',[FinanceController::class, 'updateReconciliation'])->name('transactions.reconcile')->middleware('can:finance.approve');

        // Cashflows
        Route::get('/cashflows',                        [FinanceController::class, 'cashflows'])->name('cashflows.index')->middleware('can:finance.cashflows.index');
        Route::post('/cashflows',                       [FinanceController::class, 'storeCashflow'])->name('cashflows.store')->middleware('can:finance.cashflows.create');
        Route::get('/cashflows/{cashflow}',             [FinanceController::class, 'showCashflow'])->name('cashflows.show')->middleware('can:finance.cashflows.show');
        Route::put('/cashflows/{cashflow}',             [FinanceController::class, 'updateCashflow'])->name('cashflows.update')->middleware('can:finance.cashflows.edit');
        Route::delete('/cashflows/{cashflow}',          [FinanceController::class, 'destroyCashflow'])->name('cashflows.destroy')->middleware('can:finance.cashflows.delete');
        Route::post('/cashflows/{cashflow}/approve',    [FinanceController::class, 'approveCashflow'])->name('cashflows.approve')->middleware('can:finance.approve');
        Route::post('/cashflows/{cashflow}/receive',    [FinanceController::class, 'receiveCashflow'])->name('cashflows.receive')->middleware('can:finance.approve');

        // Expenses
        Route::get('/expenses',                         [FinanceController::class, 'expenses'])->name('expenses.index')->middleware('can:finance.expenses.index');
        Route::post('/expenses',                        [FinanceController::class, 'storeExpense'])->name('expenses.store')->middleware('can:finance.expenses.create');
        Route::get('/plans-report',                     [FinanceController::class, 'plansReport'])->name('plans.report')->middleware('can:finance.expenses.index');
        Route::put('/expenses/{expense}',               [FinanceController::class, 'updateExpense'])->name('expenses.update')->middleware('can:finance.expenses.edit');
        Route::get('/expenses/{expense}',               [FinanceController::class, 'showExpense'])->name('expenses.show')->middleware('can:finance.expenses.show');
        Route::delete('/expenses/{expense}',            [FinanceController::class, 'destroyExpense'])->name('expenses.destroy')->middleware('can:finance.expenses.delete');
        Route::get('/expenses/{expense}/invoice',       [FinanceController::class, 'invoice'])->name('expenses.invoice')->middleware('can:finance.expenses.show');
        Route::post('/expenses/{expense}/approve',      [FinanceController::class, 'approveExpense'])->name('expenses.approve')->middleware('can:finance.approve');
        Route::post('/expenses/{expense}/defer',        [FinanceController::class, 'deferExpense'])->name('expenses.defer')->middleware('can:finance.expenses.edit');
        Route::post('/expenses/{expense}/reject',       [FinanceController::class, 'rejectExpense'])->name('expenses.reject')->middleware('can:finance.approve');
        Route::post('/expenses/{expense}/payments',     [FinanceController::class, 'storePayment'])->name('expenses.payments.store')->middleware('can:finance.payments.create');
        Route::post('/payments/{payment}/approve',      [FinanceController::class, 'approvePayment'])->name('payments.approve')->middleware('can:finance.approve');
    });

    // Items
    Route::prefix('items')->name('items.')->group(function () {
        Route::get('/',           [ItemController::class, 'index'])->name('index')->middleware('can:items.index');
        Route::get('/create',     [ItemController::class, 'create'])->name('create')->middleware('can:items.create');
        Route::post('/',          [ItemController::class, 'store'])->name('store')->middleware('can:items.create');
        Route::get('/{item}',     [ItemController::class, 'show'])->name('show')->middleware('can:items.show');
        Route::get('/{item}/edit',[ItemController::class, 'edit'])->name('edit')->middleware('can:items.edit');
        Route::put('/{item}',     [ItemController::class, 'update'])->name('update')->middleware('can:items.edit');
        Route::delete('/{item}',  [ItemController::class, 'destroy'])->name('destroy')->middleware('can:items.delete');
    });

    // Settings
    Route::prefix('settings')->name('settings.')->middleware('can:settings.index')->group(function () {
        Route::get('/',      [SiteSettingController::class, 'index'])->name('index');
        Route::post('/',     [SiteSettingController::class, 'update'])->name('update')->middleware('can:settings.edit');
        Route::post('/logo', [SiteSettingController::class, 'uploadLogo'])->name('logo')->middleware('can:settings.edit');
    });

    // Activity
    Route::get('/activity',         [ActivityLogController::class, 'index'])->name('activity.index')->middleware('can:activity.index');
    Route::post('/activity/clear',  [ActivityLogController::class, 'clear'])->name('activity.clear')->middleware('can:activity.index');

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/',                        [NotificationController::class, 'index'])->name('index');
        Route::patch('/{notification}/read',   [NotificationController::class, 'markRead'])->name('read');
        Route::post('/read-all',               [NotificationController::class, 'markAllRead'])->name('read-all');
    });
});
