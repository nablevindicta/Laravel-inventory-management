@extends('layouts.master', ['title' => 'Stok'])

@section('content')
    <x-container>
        <div class="col-12">
            <form action="{{ route('admin.stock.index') }}" method="GET">
                <x-search name="search" :value="$search ?? ''" />
            </form>
            <x-card title="DAFTAR STOK BARANG" class="card-body p-0">
                <x-table>
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
                            <tr>
                                <td class="text-center">{{ $i + $products->firstItem() }}</td>
                                <td class="text-center">
                                    <span class="avatar rounded avatar-md"
                                        style="background-image: url({{ $product->image }})"></span>
                                </td>
                                <td>{{ $product->code }}</td> 
                                <td>{{ $product->name }}</td>
                                <td>{{ optional($product->supplier)->name ?? '-' }}</td>
                                <td>{{ $product->category->name }}</td>
                                <td>{{ $product->unit }}</td>
                                <td class="text-center">{{ $product->quantity }}</td>
                                <td>
                                    {{-- Tombol Edit Stok --}}
                                    <x-button-modal :id="'edit-stock-modal-' . $product->id" icon="edit" style="mr-1" title="Edit Stok"
                                        class="btn btn-primary btn-sm text-white" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">Data tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-table>
            </x-card>
            <div class="d-flex justify-content-end mt-3">
                {{ $products->links() }}
            </div>
        </div>
    </x-container>

    {{-- PERBAIKAN STRUKTUR: Semua modal dipindahkan ke luar dari loop tabel agar HTML valid dan lebih cepat --}}
    @foreach ($products as $product)
        <x-modal :id="'edit-stock-modal-' . $product->id" title="Edit Stok Produk - {{ $product->name }}" data-modal-stock>
            <form action="{{ route('admin.stock.update', $product->id) }}" method="POST">
                @csrf
                @method('PUT')

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
                        Isi field di samping untuk <strong>koreksi stok manual</strong>. 
                        Tidak akan dicatat sebagai barang masuk/keluar.
                    </small>
                </div>

                <div class="mb-3">
                    <label for="add_stock_{{ $product->id }}" class="form-label">Tambah Stok</label>
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
                    <label for="reduce_stock_{{ $product->id }}" class="form-label">Kurangi Stok</label>
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
                </div>

                <x-button-save title="Simpan Perubahan" icon="save" class="btn btn-primary" />
            </form>
        </x-modal>
    @endforeach
@endsection

@push('scripts')
{{-- PERBAIKAN STRUKTUR & JAVASCRIPT: Script dipindahkan ke luar loop dan diperbaiki agar berfungsi untuk semua modal --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Ambil semua modal yang memiliki atribut 'data-modal-stock'
    const stockModals = document.querySelectorAll('[data-modal-stock]');

    // Loop melalui setiap modal untuk menambahkan event listener
    stockModals.forEach(modal => {
        const correctInput = modal.querySelector('input[name="corrected_stock"]');
        const addInput = modal.querySelector('input[name="add_stock"]');
        const reduceInput = modal.querySelector('input[name="reduce_stock"]');
        
        // Pastikan semua elemen input ada di dalam modal ini
        if (!correctInput || !addInput || !reduceInput) return;

        // Fungsi untuk membersihkan input 'tambah' dan 'kurangi'
        const clearAddReduce = () => {
            if (correctInput.value.trim() !== '') {
                addInput.value = '0';
                reduceInput.value = '0';
            }
        };
        
        // Fungsi untuk membersihkan input 'koreksi'
        const clearCorrect = () => {
            // Cek jika salah satu dari add/reduce diisi dan BUKAN 0
            if (Number(addInput.value) > 0 || Number(reduceInput.value) > 0) {
                correctInput.value = '';
            }
        };

        correctInput.addEventListener('input', clearAddReduce);
        addInput.addEventListener('input', clearCorrect);
        reduceInput.addEventListener('input', clearCorrect);
    });
});
</script>
@endpush