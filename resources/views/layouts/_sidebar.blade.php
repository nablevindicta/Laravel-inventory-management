<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Inventory System</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons@latest/iconfont/tabler-icons.min.css">

</head>

<style>
    /* Target semua link menu di dalam sidebar vertikal ini */
    .navbar-vertical .nav-link {
        font-size: 1.1rem; /* Ukuran font default biasanya sekitar 0.875rem */
    }
    
    /* Target ikon di dalam link menu agar ukurannya juga menyesuaikan */
    .navbar-vertical .nav-link-icon i {
        font-size: 1.25rem; /* Perbesar sedikit ikonnya */
    }

    /* Target judul grup menu */
    .navbar-vertical .hr-text {
        font-size: 0.85rem; /* Sedikit lebih besar dari default */
    }
</style>


<aside class="navbar navbar-vertical navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <!-- Logo / Brand -->
        <a href="{{ route('admin.dashboard') }}" class="navbar-brand">
            <h2 class="mb-0 fw-bold text-center">INVENTORY<br>GUDANG</h2>
        </a>

        <!-- Toggle untuk mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- User & Logout (Mobile View) -->
        <div class="navbar-nav flex-row d-lg-none text-size">
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex align-items-center text-reset p-0" data-bs-toggle="dropdown">
                    <span class="avatar me-2" style="background-image: url({{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }})"></span>
                    <div>
                        <div class="fw-bold">{{ Auth::user()->name }}</div>
                        <small class="text-muted">{{ Auth::user()->roles->pluck('name')->first() }}</small>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="{{ route('admin.setting.index') }}">
                        <i class="ti ti-user me-2"></i> Profil Saya
                    </a>
                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="ti ti-logout me-2"></i> Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                </div>
            </div>
        </div>

        <!-- Menu Utama -->
        <div class="collapse navbar-collapse" id="navbar-menu">
            <ul class="navbar-nav pt-lg-3">

                <!-- Dashboard -->
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}"
                       href="{{ route('admin.dashboard') }}">
                        <span class="nav-link-icon me-2" ><i class="ti ti-dashboard"></i></span>
                        Dashboard
                    </a>
                </li>

                <!-- Menu -->
                <div class="hr-text hr-text-left ml-2 mb-2 mt-4">Data Master</div>

                @can('index-category')
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('admin.category*') ? 'active' : '' }}"
                           href="{{ route('admin.category.index') }}">
                            <span class="nav-link-icon me-2"><i class="ti ti-copy"></i></span>
                            Kategori
                        </a>
                    </li>
                @endcan

                @can('index-supplier')
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('admin.supplier*') ? 'active' : '' }}"
                           href="{{ route('admin.supplier.index') }}">
                            <span class="nav-link-icon me-2"><i class="ti ti-truck"></i></span>
                            Supplier
                        </a>
                    </li>
                @endcan

                @can('index-product')
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('admin.product*') ? 'active' : '' }}"
                           href="{{ route('admin.product.index') }}">
                            <span class="nav-link-icon me-2"><i class="ti ti-package"></i></span>
                            Barang
                        </a>
                    </li>
                @endcan

                <!-- Stok -->
                <div class="hr-text hr-text-left ml-2 mb-2 mt-4">Manajemen Stok</div>

                <li class="nav-item">
                    <a class="nav-link {{ Route::is('admin.stock.*') ? 'active' : '' }}"
                       href="{{ route('admin.stock.index') }}">
                        <span class="nav-link-icon me-2"><i class="ti ti-clipboard-list"></i></span>
                        Stok Barang
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Route::is('admin.stockopname.*') ? 'active' : '' }}"
                       href="{{ route('admin.stockopname.index') }}">
                        <span class="nav-link-icon me-2"><i class="ti ti-clipboard-check"></i></span>
                        Stok Opname
                    </a>
                </li>

                <!-- Transaksi -->
                <div class="hr-text hr-text-left ml-2 mb-2 mt-4">Transaksi</div>

                <!-- Barang Masuk -->
                @can('view-incoming-stock') <!-- Ganti dengan permission yang sesuai di sistem kamu -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/transaction/product') && request('type') === 'in' ? 'active' : '' }}"
                       href="{{ route('admin.transaction.product') }}?type=in">
                        <span class="nav-link-icon me-2">
                            <i class="ti ti-shopping-cart-plus"></i>
                        </span>
                        Barang Masuk
                    </a>
                </li>
                @endcan

                <!-- Barang Keluar -->
                @can('view-outgoing-stock') <!-- Ganti dengan permission yang sesuai -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/transaction/product') && request('type') === 'out' ? 'active' : '' }}"
                       href="{{ route('admin.transaction.product') }}?type=out">
                        <span class="nav-link-icon me-2">
                            <i class="ti ti-shopping-cart-x"></i>
                        </span>
                        Barang Keluar
                    </a>
                </li>
                @endcan

                <!-- Super Admin Only -->
                @hasanyrole('Super Admin')
                    <div class="hr-text hr-text-left ml-2 mb-2 mt-4">Manajemen User</div>

                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('admin.user*') ? 'active' : '' }}"
                           href="{{ route('admin.user.index') }}">
                            <span class="nav-link-icon me-2"><i class="ti ti-users"></i></span>
                            User
                        </a>
                    </li>
                @endhasanyrole

            </ul>

            <!-- Profil & Logout (Desktop - Sticky di bawah) -->
            <div class="mt-auto pt-3 b">
                <div class="d-flex align-items-center p-3 text-white">
                    <span class="avatar me-3" style="background-image: url({{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }})"></span>
                    <div class="flex-fill">
                        <div class="fw-bold">{{ Auth::user()->name }}</div>
                        <small class="text-muted">{{ Auth::user()->roles->pluck('name')->first() }}</small>
                    </div>
                </div>

                <ul class="nav flex-column px-3">
                    <li class="nav-item">
                        <a class="nav-link py-2 {{ Route::is('admin.setting.index') ? 'active' : '' }}"
                           href="{{ route('admin.setting.index') }}">
                            <span class="nav-link-icon me-2"><i class="ti ti-settings"></i></span>
                            Profil Saya
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-2 text-danger" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <span class="nav-link-icon me-2"><i class="ti ti-logout"></i></span>
                            Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</aside>
