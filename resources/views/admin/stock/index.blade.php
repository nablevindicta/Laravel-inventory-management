@extends('layouts.master', ['title' => 'Stok'])

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
            
                <!-- Card Total Stok -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body d-flex align-items-center">
                            <div class="bg-warning text-white p-3 rounded me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="icon">
                                    <path d="M2 3h20v18H2z"></path>
                                    <path d="M6 7h12"></path>
                                    <path d="M6 11h12"></path>
                                    <path d="M6 15h8"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="mb-1 text-muted">Total Stok Barang</p>
                                <h4 class="mb-0">{{ $products->sum('quantity') ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Daftar Stok Barang -->
            <div class="card shadow-sm mb-4">
                <!-- Header -->
                <div class="card-header bg-white border-bottom d-flex align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-box-seam text-primary me-2"></i>
                        <strong>DAFTAR STOK BARANG</strong>
                    </div>
                    <!-- Tidak ada tombol tambah di halaman stok, biarkan kosong -->
                </div>

                <!-- Body: Pencarian + Tabel + Pagination -->
                <div class="card-body">
                    
                    <!-- Form Pencarian (tetap ada untuk server-side fallback) -->
                    <form action="{{ route('admin.stock.index') }}" method="GET" id="searchForm">
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
                        <table class="table table-hover table-striped mb-3" id="stockTable">
                            <thead>
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>Foto</th>
                                    <th>Kode</th> 
                                    <th>Nama Barang</th>
                                    <th>Nama Supplier</th>
                                    <th>Kategori Barang</th>
                                    <th>Satuan</th>
                                    <th>Stok</th>
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
                                        <td class="text-center">{{ $product->quantity }}</td>
                                        <td class="text-center">
                                            {{-- Tombol Edit Stok --}}
                                            <x-button-modal :id="'edit-stock-modal-' . $product->id" icon="edit" style="mr-1" title="Edit Stok"
                                                class="btn btn-primary btn-sm text-white" />
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="no-data-row">
                                        <td colspan="9" class="text-center">Data tidak ditemukan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination (tetap tampil, tapi realtime search hanya filter di halaman ini) -->
                    <div class="d-flex justify-content-end">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </x-container>

    {{-- Modal Edit Stok (tetap di luar loop untuk validitas HTML) --}}
    @foreach ($products as $product)
        {{-- Ganti <x-modal> untuk edit stok Anda dengan kode ini --}}

    <x-modal :id="'edit-stock-modal-' . $product->id" title="Edit Stok Produk" data-modal-stock>
        <form action="{{ route('admin.stock.update', $product->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- DITAMBAHKAN: Bagian untuk menampilkan info & gambar produk --}}
            <div class="mb-4 text-center">
                <span class="avatar rounded avatar-md"
                    style="background-image: url('{{ asset($product->image) }}'); width: 200px; height: 200px;"></span>
                    <h5 class="mb-0">{{ $product->name }}</h5>
            </div>        
            <hr>
            {{-- Sisa form Anda (tidak ada perubahan di sini) --}}
            <div class="mb-3">
                <label class="form-label">Stok Saat Ini</label>
                <div class="input-group">
                    <span class="input-group-text bg-primary text-white">
                        {{ $product->quantity }}
                    </span>
                    <input type="number"
                        name="corrected_stock"
                        class="form-control @error('corrected_stock') is-invalid @enderror"
                        min="0"
                        value=""
                        placeholder="Masukkan stok yang benar">
                </div>
                @error('corrected_stock')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <small class="text-muted">
                    Isi kolom diatas untuk <strong>koreksi stok manual</strong>. 
                    Tidak akan dicatat sebagai barang masuk/keluar (diisi apabila ada kesalahan input).
                </small>
            </div>

            <div class="mb-3">
                <label for="add_stock_{{ $product->id }}" class="form-label">Tambah Stok (Barang Masuk)</label>
                <input type="number"
                    name="add_stock"
                    id="add_stock_{{ $product->id }}"
                    class="form-control @error('add_stock') is-invalid @enderror"
                    min="0"
                    value="{{ old('add_stock', 0) }}"
                    placeholder="Masukkan jumlah untuk ditambahkan">
                @error('add_stock')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Stok akan bertambah. Harus 0 atau lebih.</small>
            </div>

            <div class="mb-3">
                <label for="reduce_stock_{{ $product->id }}" class="form-label">Kurangi Stok (Barang Keluar)</label>
                <input type="number"
                    name="reduce_stock"
                    id="reduce_stock_{{ $product->id }}"
                    class="form-control @error('reduce_stock') is-invalid @enderror"
                    min="0"
                    max="{{ $product->quantity }}"
                    value="{{ old('reduce_stock', 0) }}"
                    placeholder="Masukkan jumlah untuk dikurangi">
                @error('reduce_stock')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                
                <small class="text-muted">Maksimal yang bisa dikurangi: {{ $product->quantity }}</small>
                <div class="mt-2">
                    <label for="description_{{ $product->id }}" class="form-label">Deskripsi</label>
                    <textarea 
                        name="description" 
                        id="description_{{ $product->id }}"
                        class="form-control @error('description') is-invalid @enderror"
                        rows="2"
                        placeholder="Contoh: Digunakan oleh OB untuk kebutuhan kegiatan">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <x-button-save title="Simpan Perubahan" icon="save" class="btn btn-primary" />
        </form>
    </x-modal>
    @endforeach
@endsection
