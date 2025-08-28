@extends('layouts.master', ['title' => 'Stok'])

@section('content')
    <x-container>
        <div class="col-12">
            <form action="{{ route('admin.stock.index') }}" method="GET">
                <x-search name="search" :value="$search" />
            </form>
            <x-card title="DAFTAR BARANG" class="card-body p-0">
                <x-table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Foto</th>
                            <th>Nama Barang</th>
                            <th>Nama Supplier</th>
                            <th>Kategori Barang</th>
                            <th>Satuan</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $i => $product)
                            <tr>
                                <td>{{ $i + $products->firstItem() }}</td>
                                <td>
                                    <span class="avatar rounded avatar-md"
                                        style="background-image: url({{ $product->image }})"></span>
                                </td>
                                <td>{{ $product->name }}</td>
                                <td>{{ optional($product->supplier)->name ?? '-' }}</td>
                                <td>{{ $product->category->name }}</td>
                                <td>{{ $product->unit }}</td>
                                <td>{{ $product->quantity }}</td>
                                <td>
                                    {{-- Tombol dan Modal Edit Stok --}}
                                    <x-button-modal :id="$product->id" icon="edit" style="mr-1" title="Edit Stok"
                                        class="btn bg-teal btn-sm text-white" />

                                    <x-modal :id="$product->id" title="Edit Stok Produk - {{ $product->name }}">
                                        <form action="{{ route('admin.stock.update', $product->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')

                                           <!-- Stok Saat Ini (Bisa Diedit Langsung) -->
                                            <div class="mb-3">
                                                <label class="form-label">Stok Saat Ini</label>
                                                <input type="number"
                                                       name="current_stock"
                                                       class="form-control"
                                                       min="0"
                                                       value="{{ old('current_stock', $product->quantity) }}"
                                                       placeholder="Masukkan stok terbaru">
                                            </div>

                                            <!-- Tambah Stok -->
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

                                            <!-- Kurangi Stok -->
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

                                            <!-- Tombol Simpan -->
                                            <x-button-save title="Simpan Perubahan" icon="save" class="btn btn-primary" />
                                        </form>
                                    </x-modal>
                                </td>
                            </tr>

                            @push('scripts')
                            <script>
                                // JavaScript untuk memastikan hanya satu input yang aktif
                                document.addEventListener('DOMContentLoaded', function () {
                                    const addInput = document.getElementById('add_stock_{{ $product->id }}');
                                    const reduceInput = document.getElementById('reduce_stock_{{ $product->id }}');

                                    if (addInput && reduceInput) {
                                        addInput.addEventListener('input', function () {
                                            if (this.value.trim() !== '' && parseInt(this.value) > 0) {
                                                reduceInput.value = '';
                                            }
                                        });

                                        reduceInput.addEventListener('input', function () {
                                            if (this.value.trim() !== '' && parseInt(this.value) > 0) {
                                                addInput.value = '';
                                            }
                                        });
                                    }
                                });
                            </script>
                            @endpush
                        @endforeach
                    </tbody>
                </x-table>
            </x-card>
        </div>
    </x-container>
@endsection