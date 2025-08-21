@extends('layouts.master', ['title' => 'Stok'])

@section('content')
    <x-container>
        <div class="col-12">
            <x-card title="DAFTAR PRODUK" class="card-body p-0">
                <x-table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Foto</th>
                            <th>Nama Produk</th>
                            <th>Nama Supplier</th>
                            <th>Kategori Produk</th>
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
                                    {{-- Tombol Tambah Stok --}}
                                    <x-button-modal :id="$product->id" icon="plus" style="mr-1" title="Edit Stok"
                                        class="btn bg-teal btn-sm text-white" />
                                    <x-modal :id="$product->id" title="Edit Stok Produk - {{ $product->name }}">
                                        <form action="{{ route('admin.stock.update', $product->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <x-input title="Stok Saat Ini" name="quantity" type="number" min="0"
                                                :value="$product->quantity" />
                                            <x-button-save title="Simpan" icon="save" class="btn btn-primary" />
                                        </form>
                                    </x-modal>

                                    {{-- Tombol Kurangi Stok --}}
                                    <x-button-modal :id="'reduce-' . $product->id" icon="minus" style="mr-1" title="Kurangi Stok"
                                        class="btn bg-red btn-sm text-white" />
                                    <x-modal :id="'reduce-' . $product->id" title="Kurangi Stok - {{ $product->name }}">
                                        <form action="{{ route('admin.stock.update', $product->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="action" value="reduce">
                                            <x-input title="Jumlah yang Dikurangi" name="quantity" type="number" min="1"
                                                max="{{ $product->quantity }}" placeholder="Masukkan jumlah"
                                                :value="1" />
                                            <small class="text-muted">Stok saat ini: {{ $product->quantity }}</small>
                                            <x-button-save title="Kurangi" icon="minus" class="btn btn-danger mt-2" />
                                        </form>
                                    </x-modal>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-table>
            </x-card>
        </div>
    </x-container>
@endsection
