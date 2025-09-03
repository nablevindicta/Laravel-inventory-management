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
                        @foreach ($products as $i => $product)
                            <tr>
                                <td class="text-center">{{ $i + $products->firstItem() }}</td>
                                <td class="text-center">
                                    <span class="avatar rounded avatar-md"
                                        style="background-image: url({{ $product->image }})"></span>
                                </td>
                                <td>{{ $product->code }}</td>
                                <td >{{ $product->name }}</td>
                                <td>{{ optional($product->supplier)->name ?? '-' }}</td>
                                <td>{{ $product->category->name }}</td>
                                <td>{{ $product->unit }}</td>
                                <td>{{ $product->quantity }}</td>
                                <td>
                                    {{-- Tombol dan Modal Edit Stok --}}
                                    <x-button-modal :id="$product->id" icon="edit" style="mr-1" title="Edit Stok"
                                        class="btn btn-primary btn-sm text-white" />

                                    <x-modal :id="$product->id" title="Edit Stok Produk - {{ $product->name }}">
                                        <form action="{{ route('admin.stock.update', $product->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')

                                           <!-- Stok Saat Ini & Koreksi Langsung -->
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
                            document.addEventListener('DOMContentLoaded', function () {
                                const correctInput = document.querySelector('input[name="corrected_stock"]');
                                const addInput = document.getElementById('add_stock_{{ $product->id }}');
                                const reduceInput = document.getElementById('reduce_stock_{{ $product->id }}');
                            
                                if (!correctInput) return;
                            
                                const clearAddReduce = () => {
                                    if (correctInput.value.trim() !== '') {
                                        addInput.value = '';
                                        reduceInput.value = '';
                                    }
                                };
                            
                                const clearCorrect = () => {
                                    if (addInput.value.trim() !== '' || reduceInput.value.trim() !== '') {
                                        correctInput.value = '';
                                    }
                                };
                            
                                correctInput.addEventListener('input', clearAddReduce);
                                addInput.addEventListener('input', clearCorrect);
                                reduceInput.addEventListener('input', clearCorrect);
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