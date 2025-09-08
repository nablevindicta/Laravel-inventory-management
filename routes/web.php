<?php

use App\Http\Controllers\Admin\{
    DashboardController, CategoryController, PermissionController, SupplierController,
    ProductController, RoleController, StockController, TransactionController,
    UserController, OrderController,
    SettingController,
    StockOpnameController
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// === Redirect landing ke login ===
Route::redirect('/', '/login')->name('landing');

// === Halaman Login (GET) ===
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// === Proses Login (POST) ===
Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
        $request->session()->regenerate();
        // Middleware akan menangani pengecekan peran. Langsung arahkan ke dashboard.
        return redirect()->intended('/admin/dashboard');
    }

    return back()->withErrors([
        'email' => 'Email atau password salah.',
    ])->withInput($request->only('email'));
})->name('login');

// === Logout ===
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// ========================================================================
// === ADMIN PANEL (Hanya untuk Admin dan Super Admin)
// ========================================================================

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth', 'role:Admin|Super Admin']], function () {
    
    Route::get('/dashboard', DashboardController::class)
        ->name('dashboard')
        ->middleware('permission:index-dashboard');

    Route::resource('/category', CategoryController::class)
        ->except('show', 'create', 'edit')
        ->middleware('permission:index-category');

    Route::resource('/supplier', SupplierController::class)
        ->except('show', 'create', 'edit')
        ->middleware('permission:index-supplier');

    Route::resource('/product', ProductController::class)
        ->except('show')
        ->middleware('permission:index-product');

    Route::resource('/stock', StockController::class)
        ->only('index', 'update')
        ->middleware('permission:index-stock');

    Route::resource('/user', UserController::class)
        ->middleware('permission:index-user');

    Route::group(['middleware' => ['role:Super Admin']], function () {
        Route::get('/user/create', [UserController::class, 'create'])->name('user.create');
        Route::post('/user', [UserController::class, 'store'])->name('user.store');
        Route::put('/user/{user}', [UserController::class, 'update'])->name('user.update');
        Route::delete('/user/{user}', [UserController::class, 'destroy'])->name('user.destroy');
    });

    Route::resource('/role', RoleController::class)
        ->middleware('role:Super Admin');

    Route::resource('/permission', PermissionController::class)
        ->except('show', 'create', 'edit')
        ->middleware('role:Super Admin');

    Route::controller(TransactionController::class)->group(function () {
        Route::get('/transaction/product', 'product')->name('transaction.product');
        Route::delete('/transaction/{transaction}', 'destroy')->name('transaction.destroy');
        Route::get('/transaction/{type}/pdf', 'exportPdf')->name('transaction.pdf');
    });

    Route::controller(SettingController::class)->group(function () {
        Route::get('/setting', 'index')->name('setting.index');
        Route::put('/setting/update/{user}', 'update')->name('setting.update');
    });

    Route::controller(StockOpnameController::class)->group(function () {
        Route::get('/stock-opname', 'index')->name('stockopname.index');
        Route::post('/stock-opname', 'store')->name('stockopname.store');
        Route::get('/stock-opname/pdf', 'exportPdf')->name('stockopname.pdf');
        Route::get('/stockopname/{stockOpnameSession}/pdf', 'exportDetailPdf')->name('stockopname.pdf_detail');
        Route::delete('/stockopname/{stockOpnameSession}','destroy')->name('stockopname.destroy');
    });
});