@extends('layouts.master', ['title' => 'Dashboard'])

@section('content')
    <!-- Background Header Biru + Kartu Mengambang -->
    <div class="position-relative bg-primary text-white" style="border-radius: 0 0 20px 20px; padding-top: 1.5rem; padding-bottom: 4rem;">
        <!-- Judul Header -->
        <div class="px-4 pb-3">
            <div class="d-flex align-items-center">
                <div class="bg-opacity-20 p-2 rounded me-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon text-white">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                </div>
                <h4 class="mb-0 fw-light">Dashboard</h4>
            </div>
        </div>

        <!-- Row 1: 3 Kartu Besar (Mengambang) -->
        <div class="position-absolute start-0 end-0 px-3" style="top: 70%; z-index: 1;">
            <div class="row g-3">
                <!-- Data Barang -->
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body d-flex align-items-center p-3">
                            <div class="me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="icon text-primary">
                                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                    <line x1="8" y1="21" x2="16" y2="21"></line>
                                    <line x1="12" y1="17" x2="12" y2="21"></line>
                                </svg>
                            </div>
                            <div>
                                <p class="text-muted mb-1">Data Barang</p>
                                <h5 class="mb-0">{{ $products }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Barang Masuk -->
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body d-flex align-items-center p-3">
                            <div class="me-3">
                                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="green"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-basket-up"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 10l-2 -6" /><path d="M7 10l2 -6" /><path d="M12 20h-4.756a3 3 0 0 1 -2.965 -2.544l-1.255 -7.152a2 2 0 0 1 1.977 -2.304h13.999a2 2 0 0 1 1.977 2.304l-.358 2.04" /><path d="M10 14a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M19 22v-6" /><path d="M22 19l-3 -3l-3 3" /></svg>
                            </div>
                            <div>
                                <p class="text-muted mb-1">Total Barang Masuk</p>
                                <h5 class="mb-0">{{ $productInQuantity }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Barang Keluar -->
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body d-flex align-items-center p-3">
                            <div class="me-3">
                                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="red"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-basket-down"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 10l-2 -6" /><path d="M7 10l2 -6" /><path d="M12 20h-4.756a3 3 0 0 1 -2.965 -2.544l-1.255 -7.152a2 2 0 0 1 1.977 -2.304h13.999a2 2 0 0 1 1.977 2.304l-.349 1.989" /><path d="M10 14a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M19 16v6" /><path d="M22 19l-3 3l-3 -3" /></svg>
                            </div>
                            <div>
                                <p class="text-muted mb-1">Total Barang Keluar</p>
                                <h5 class="mb-0">{{ $productOutQuantity }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid px-3 mt-5">
        <!-- Row 2: 6 Kartu Kecil (Sejajar dengan Row 1) -->
        <div class="row mb-4 mt-5">
            <div class="col-sm-6 col-md-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center p-2">
                        <div class="me-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="icon text-primary">
                                <rect x="8" y="8" width="12" height="12" rx="2"></rect>
                                <path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="mb-1 small text-muted">Kategori</p>
                            <h6 class="mb-0">{{ $categories }}</h6>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center p-2">
                        <div class="me-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="icon text-info">
                                <circle cx="7" cy="17" r="2"></circle>
                                <circle cx="17" cy="17" r="2"></circle>
                                <path d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="mb-1 small text-muted">Supplier</p>
                            <h6 class="mb-0">{{ $suppliers }}</h6>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center p-2">
                        <div class="me-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="icon text-success">
                                <path d="M2 3h1a2 2 0 0 1 2 2v10a2 2 0 0 0 2 2h15"></path>
                                <rect x="9" y="6" width="10" height="8" rx="3"></rect>
                                <circle cx="9" cy="19" r="2"></circle>
                                <circle cx="18" cy="19" r="2"></circle>
                            </svg>
                        </div>
                        <div>
                            <p class="mb-1 small text-muted">Barang</p>
                            <h6 class="mb-0">{{ $products }}</h6>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center p-2">
                        <div class="me-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="icon text-danger">
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                <path d="M21 21v-2a4 4 0 0 0 -3 -3.85"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="mb-1 small text-muted">User</p>
                            <h6 class="mb-0">{{ $customers }}</h6>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center p-2">
                        <div class="me-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="icon text-teal">
                                <path d="M12 5v14"></path>
                                <polyline points="8 12 12 16 16 12"></polyline>
                            </svg>
                        </div>
                        <div>
                            <p class="mb-1 small text-muted">Masuk Bulan Ini</p>
                            <h6 class="mb-0">{{ $productInThisMonth }}</h6>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center p-2">
                        <div class="me-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="icon text-orange">
                                <path d="M12 19v-14"></path>
                                <polyline points="8 12 12 8 16 12"></polyline>
                            </svg>
                        </div>
                        <div>
                            <p class="mb-1 small text-muted">Keluar Bulan Ini</p>
                            <h6 class="mb-0">{{ $productOutThisMonth }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 3: Tabel Stok Rendah (Sejajar) -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle text-primary me-2"></i>
                    <strong>Stok barang telah mencapai batas minimum</strong>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <label class="me-2">Tampilkan</label>
                            <select id="perPageSelect" class="form-select form-select-sm w-auto" onchange="changePerPage()">
                                <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                            </select>
                            <span class="ms-2">data</span>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Cari:</span>
                            <input type="text" id="searchInput" class="form-control" placeholder="Cari barang...">
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="lowStockTable" class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Kategori Barang</th>
                                <th>Stok</th>
                                <th>Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($productsOutStock as $index => $product)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $product->code }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->category->name }}</td>
                                    <td>
                                        <span class="badge bg-warning text-dark">{{ $product->quantity }}</span>
                                    </td>
                                    <td>{{ $product->unit }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small class="text-muted">
                        Menampilkan {{ $productsOutStock->firstItem() ?? 0 }} sampai {{ $productsOutStock->lastItem() ?? 0 }}
                        dari {{ $productsOutStock->total() }} data
                    </small>
                    <nav>
                        {{ $productsOutStock->links() }}
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
<style>
    .text-teal { color: #008080; }
    .text-orange { color: #FF8C00; }
    .text-info { color: #17a2b8; }
    .text-success { color: #28a745; }
    .text-danger { color: #dc3545; }
    .text-primary { color: #007bff; }
    .shadow-sm {
        box-shadow: 0 0.15rem 0.25rem rgba(0,0,0,0.1);
    }
    .shadow-lg {
        box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.15);
    }
    .card {
        border-radius: 8px;
    }
    .card-body {
        padding: 0.5rem;
    }
    .icon {
        width: 32px;
        height: 32px;
    }
    /* Pastikan semua konten sejajar */
    .container-fluid,
    .position-absolute {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    @media (max-width: 576px) {
        .container-fluid, .position-absolute {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
    }
</style>
@endpush

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const table = document.getElementById('lowStockTable');
        const tbody = table.querySelector('tbody');
        const rows = tbody.querySelectorAll('tr');

        searchInput.addEventListener('keyup', function () {
            const searchText = searchInput.value.toLowerCase().trim();

            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                let found = false;

                cells.forEach(cell => {
                    const text = cell.textContent.toLowerCase();
                    if (text.includes(searchText)) {
                        found = true;
                    }
                });

                row.style.display = found ? '' : 'none';
            });
        });
    });
</script>
@endpush

@push('js')
<script>
    function changePerPage() {
        const perPage = document.getElementById('perPageSelect').value;
        const url = new URL(window.location.href);
        
        // Set atau update parameter per_page
        url.searchParams.set('per_page', perPage);
        // Reset ke halaman 1 saat ganti jumlah per page
        url.searchParams.set('page', '1');

        // Redirect
        window.location.href = url.toString();
    }
</script>
@endpush