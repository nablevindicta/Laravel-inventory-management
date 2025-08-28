<?php

use App\Http\Controllers\Admin\{
    DashboardController, CategoryController, PermissionController, SupplierController,
    ProductController, RoleController, StockController, TransactionController,
    UserController, OrderController,
    SettingController,
    StockOpnameController
};
use App\Http\Controllers\Customer\{
    DashboardController as CustomerDashboardController, OrderController as CustomerOrderController, TransactionController as CustomerTransactionController, RentController as CustomerRentController,
    SettingController as CustomerSettingController
};
use App\Http\Controllers\{
    LandingController, ProductController as LandingProductController,
    TransactionController as LandingTransactionController,
    CategoryController as LandingCategoryController, VehicleController as LandingVehicleController
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// === Redirect landing ke login ===
Route::redirect('/', '/login')->name('landing');

// === Halaman Login (GET) ===
Route::get('/login', function () {
    return view('auth.login');
})->name('login'); // ⚠️ Nama route HARUS 'login' karena blade pakai {{ route('login') }}

// === Proses Login (POST) ===
Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
        $request->session()->regenerate();

        $user = Auth::user();

        // Redirect berdasarkan role
        if ($user->hasRole('Admin') || $user->hasRole('Super Admin')) {
            return redirect()->intended('/admin/dashboard');
        } elseif ($user->hasRole('Customer')) {
            return redirect()->intended('/customer/dashboard');
        }

        return redirect()->intended('/'); // fallback
    }

    return back()->withErrors([
        'email' => 'Email atau password salah.',
    ])->withInput($request->only('email'));
})->name('login'); // ⚠️ Gunakan name 'login' untuk POST juga (bisa, karena method beda)

// === Logout ===
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

Route::controller(LandingCategoryController::class)->as('category.')->group(function(){
    Route::get('/category', 'index')->name('index');
    Route::get('/category/{slug}', 'show')->name('show');
});

Route::controller(LandingProductController::class)->as('product.')->group(function(){
    Route::get('/product', 'index')->name('index');
    Route::get('/product/{slug}', 'show')->name('show');
});

Route::controller(LandingVehicleController::class)->as('vehicle.')->group(function(){
    Route::get('/vehicle', 'index')->name('index')->middleware('permission:index-rent');
    Route::post('/vehicle', 'store')->name('store')->middleware(['permission:create-rent','auth']);
});


Route::post('/transaction', [LandingTransactionController::class, 'store'])
    ->middleware(['permission:create-transaction','auth'])->name('transaction.store');

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

    Route::resource('/order', OrderController::class)
        ->middleware('permission:index-order');

    Route::resource('/user', UserController::class)
        ->middleware('permission:index-user');

    Route::resource('/role', RoleController::class)
        ->middleware('permission:index-role');

    Route::resource('/permission', PermissionController::class)
        ->except('show', 'create', 'edit')
        ->middleware('permission:index-permission');

    Route::controller(TransactionController::class)->group(function(){
        Route::get('/transaction/product', 'product')->name('transaction.product');
        Route::get('/transaction/productin', 'productin')->name('transaction.productin');
        Route::delete('/transaction/{transaction}', 'destroy')->name('transaction.destroy');

        Route::get('/transaction/{type}/pdf', 'exportPdf')->name('transaction.pdf');


        
    });

    // Route::controller(ReportController::class)->group(function(){
    //     Route::get('/report', 'index')->name('report');
    // });

    Route::controller(SettingController::class)->group(function(){
        Route::get('/setting', 'index')->name('setting.index');
        Route::put('/setting/update/{user}', 'update')->name('setting.update');
    });

    Route::controller(StockOpnameController::class)->group(function() {
        Route::get('/stock-opname', 'index')->name('stockopname.index');
        Route::get('/stock-opname/create', 'create')->name('stockopname.create'); // <-- BARIS INI DITAMBAHKAN
        Route::post('/stock-opname', 'store')->name('stockopname.store');
        Route::get('/stock-opname/pdf', 'exportPdf')->name('stockopname.pdf');
    });

});

Route::group(['prefix' => 'customer', 'as' => 'customer.', 'middleware' => ['auth', 'role:Customer']], function (){
    Route::get('/dashboard', CustomerDashboardController::class)->name('dashboard');
    Route::get('/transaction', CustomerTransactionController::class)->name('transaction');
    Route::resource('/order', CustomerOrderController::class);
    Route::resource('/rent', CustomerRentController::class);
    Route::controller(CustomerSettingController::class)->group(function(){
        Route::get('/setting', 'index')->name('setting.index');
        Route::put('/setting/update/{user}', 'update')->name('setting.update');
    });
});
