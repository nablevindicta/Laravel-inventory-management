@extends('layouts.master', ['title' => 'Barang'])

@section('content')
    <x-container>
        <div class="col-12">

            <!-- Dua Kartu Informasi -->
            <div class="row mb-4">
                <!-- Card Jumlah Kategori -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body d-flex align-items-center">
                            <div class="bg-primary text-white p-3 rounded me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="icon">
                                    <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                                    <path d="M2 17l10 5 10-5"></path>
                                    <path d="M2 12l10 5 10-5"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="mb-1 text-muted">Kategori Barang</p>
                                <h4 class="mb-0">{{ $categories->count() ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Total Produk -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body d-flex align-items-center">
                            <div class="bg-success text-white p-3 rounded me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="icon">
                                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                    <line x1="8" y1="21" x2="16" y2="21"></line>
                                    <line x1="12" y1="17" x2="12" y2="21"></line>
                                </svg>
                            </div>
                            <div>
                                <p class="mb-1 text-muted">Total Produk</p>
                                <h4 class="mb-0">{{ $products->total() ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Daftar Barang -->
            <div class="card shadow-sm mb-4">
                <!-- Header -->
                <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-box-seam text-primary me-2"></i>
                        <strong>DAFTAR BARANG</strong>
                    </div>
                    <!-- Tombol Tambah Barang -->
                    @can('create-product')
                        <x-button-modal id="create-product-modal" title="Tambah Barang" icon="plus"
                            class="btn btn-primary btn-sm" style="" />
                    @endcan
                </div>

                <!-- Body: Pencarian + Tabel + Pagination -->
                <div class="card-body">
                    
                    <!-- Form Pencarian -->
                    <form action="{{ route('admin.product.index') }}" method="GET" id="searchForm">
                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-6">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Cari:</span>
                                    <input 
                                        type="text" 
                                        name="search" 
                                        class="form-control" 
                                        placeholder="Cari barang..."
                                        value="{{ $search ?? '' }}" 
                                        id="searchInput"
                                        autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Tabel -->
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-3" id="productTable">
                            <thead>
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>Foto</th>
                                    <th>Kode</th>
                                    <th>Nama Barang</th>
                                    <th>Nama Supplier</th>
                                    <th>Kategori Barang</th>
                                    <th>Satuan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($products as $i => $product)
                                    <tr class="searchable-row">
                                        <td class="text-center">{{ $i + $products->firstItem() }}</td>
                                        <td class="text-center">
                                            <span class="avatar rounded avatar-md"
                                                style="background-image: url({{ $product->image }})"></span>
                                        </td>
                                        <td class="text-center">{{ $product->code }}</td>
                                        <td class="text-center">{{ $product->name }}</td>
                                        <td class="text-center">{{ optional($product->supplier)->name ?? '-' }}</td>
                                        <td class="text-center">{{ $product->category->name }}</td>
                                        <td class="text-center">{{ $product->unit }}</td>
                                        <td class="text-center">
                                            @can('update-product')
                                                <x-button-modal :id="'edit-product-modal-' . $product->id" title="" icon="edit"
                                                    class="btn btn-info btn-sm me-1" style="" />

                                                {{-- GANTI SELURUH BLOK MODAL EDIT ANDA DENGAN KODE INI --}}

                                                <x-modal :id="'edit-product-modal-' . $product->id" title="Edit Produk - {{ $product->name }}">
                                                    <form action="{{ route('admin.product.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')

                                                        {{-- BAGIAN BARU UNTUK PREVIEW GAMBAR --}}
                                                        <div class="mb-4 text-center">
                                                            <span class="avatar rounded avatar-md"
                                                                style="background-image: url('{{ asset($product->image) }}'); width: 200px; height: 200px;"></span>
                                                        </div>
                                                        <hr>
                                                        
                                                        <x-input name="name" type="text" title="Nama Produk" placeholder="Nama Produk" :value="$product->name" />
                                                        
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <x-select title="Kategori Produk" name="category_id">
                                                                    <option value="">Silahkan Pilih</option>
                                                                    @foreach ($categories as $category)
                                                                        <option value="{{ $category->id }}" @selected($product->category_id == $category->id)>
                                                                            {{ $category->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </x-select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <x-select title="Supplier Produk" name="supplier_id">
                                                                    <option value="">Silahkan Pilih</option>
                                                                    @foreach ($suppliers as $supplier)
                                                                        <option value="{{ $supplier->id }}" @selected($product->supplier_id == $supplier->id)>
                                                                            {{ $supplier->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </x-select>
                                                            </div>
                                                        </div>

                                                        <div class="row mt-3">
                                                            <div class="col-md-6">
                                                                {{-- Input file sekarang terpisah dari preview --}}
                                                                <x-input name="image" type="file" title="Ganti Foto Produk (Opsional)" />
                                                            </div>
                                                            <div class="col-md-6">
                                                                <x-input name="unit" type="text" title="Satuan Produk" placeholder="Satuan Produk" :value="$product->unit" />
                                                            </div>
                                                        </div>

                                                        <x-textarea name="description" title="Deskripsi Produk" placeholder="Deskripsi Produk">
                                                            {{ $product->description }}
                                                        </x-textarea>
                                                        
                                                        <x-button-save title="Simpan" icon="save" class="btn btn-primary mt-3" />
                                                    </form>
                                                </x-modal>
                                            @endcan

                                            @can('delete-product')
                                                <x-button-delete :id="$product->id" :url="route('admin.product.destroy', $product->id)"
                                                    title="" class="btn btn-danger btn-sm" />
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="no-data-row">
                                        <td colspan="8" class="text-center">Data barang tidak ditemukan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-end">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </x-container>

    {{-- Modal Tambah Barang --}}
    <x-modal id="create-product-modal" title="Tambah Barang">
        <form action="{{ route('admin.product.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <x-input name="name" type="text" title="Nama Produk" placeholder="Nama Produk" :value="old('name')" />
            @error('name')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            <x-input name="unit" type="text" title="Satuan Produk" placeholder="Satuan Produk" :value="old('unit')" />
            @error('unit')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            <x-select title="Supplier Barang" name="supplier_id">
                <option value="">Silahkan Pilih</option>
                @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                        {{ $supplier->name }}
                    </option>
                @endforeach
            </x-select>
            @error('supplier_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            <x-select title="Kategori Barang" name="category_id">
                <option value="">Silahkan Pilih</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </x-select>
            @error('category_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            <x-input name="image" type="file" title="Foto Barang" />
            @error('image')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            <x-textarea name="description" title="Deskripsi Barang" placeholder="Deskripsi Barang">
                {{ old('description') }}
            </x-textarea>
            @error('description')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            <div class="mt-3">
                <x-button-save title="Simpan" icon="save" class="btn btn-primary" />
            </div>
        </form>
    </x-modal>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const table = document.getElementById('productTable'); // ✅ DIPERBAIKI: definisikan 'table'
        const tbody = table.querySelector('tbody');
        const rows = tbody.querySelectorAll('.searchable-row'); // Hanya baris data
        const noDataRow = document.getElementById('no-data-row'); // Baris "Data tidak ditemukan"

        searchInput.addEventListener('keyup', function () {
            const searchText = this.value.toLowerCase().trim();
            let visibleRows = 0;

            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                let found = false;

                cells.forEach(cell => {
                    // Abaikan kolom Aksi (indeks terakhir) karena berisi tombol
                    if (cell.querySelector('.btn') || cell.querySelector('.avatar')) {
                        return;
                    }
                    const text = cell.textContent.toLowerCase();
                    if (text.includes(searchText)) {
                        found = true;
                    }
                });

                if (found) {
                    row.style.display = '';
                    visibleRows++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Tampilkan/menyembunyikan baris "Data tidak ditemukan"
            if (noDataRow) {
                noDataRow.style.display = visibleRows > 0 ? 'none' : '';
            }
        });
    });
</script>
@endpush