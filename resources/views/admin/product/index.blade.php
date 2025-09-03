@extends('layouts.master', ['title' => 'Barang'])

@section('content')
    <x-container>
        <div class="col-12">
            {{-- PERUBAHAN KRITIS: Arahkan form ke route yang benar agar controller product terpanggil --}}
            <form action="{{ route('admin.product.index') }}" method="GET">
                {{-- Variabel $search sudah dikirim dari controller yang baru --}}
                <x-search name="search" :value="$search ?? ''" />
            </form>

            @can('create-product')
                {{-- Tombol Tambah Barang diubah menjadi modal --}}
                <x-button-modal id="create-product-modal" title="Tambah Barang" icon="plus"
                    class="btn btn-primary mb-3 mr-1" style="" />
            @endcan
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
                            <th>Stok</th>
                            <th>Satuan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- PENYEMPURNAAN: Gunakan @forelse untuk menangani data kosong --}}
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
                                <td class="text-center">{{ $product->quantity }}</td>
                                <td>{{ $product->unit }}</td>
                                <td class="text-center">
                                    @can('update-product')
                                        {{-- Tombol modal edit --}}
                                        <x-button-modal :id="'edit-product-modal-' . $product->id" title="" icon="edit" style=""
                                            class="btn btn-info btn-sm mr-1" />

                                        {{-- Modal Edit Produk --}}
                                        <x-modal :id="'edit-product-modal-' . $product->id" title="Edit Produk - {{ $product->name }}">
                                            <form action="{{ route('admin.product.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <x-input name="name" type="text" title="Nama Produk" placeholder="Nama Produk" :value="$product->name" />
                                                <div class="row">
                                                    <div class="col-6">
                                                        <x-select title="Kategori Produk" name="category_id">
                                                            <option value="">Silahkan Pilih</option>
                                                            @foreach ($categories as $category)
                                                                <option value="{{ $category->id }}" @selected($product->category_id == $category->id)>
                                                                    {{ $category->name }}</option>
                                                            @endforeach
                                                        </x-select>
                                                    </div>
                                                    <div class="col-6">
                                                        <x-select title="Supplier Produk" name="supplier_id">
                                                            <option value="">Silahkan Pilih</option>
                                                            @foreach ($suppliers as $supplier)
                                                                <option value="{{ $supplier->id }}" @selected($product->supplier_id == $supplier->id)>
                                                                    {{ $supplier->name }}</option>
                                                            @endforeach
                                                        </x-select>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <x-input name="image" type="file" title="Foto Produk" placeholder="" :value="$product->image" />
                                                    </div>
                                                    <div class="col-6">
                                                        <x-input name="unit" type="text" title="Satuan Produk" placeholder="Satuan Produk" :value="$product->unit" />
                                                    </div>
                                                </div>
                                                <x-textarea name="description" title="Deskripsi Produk" placeholder="Deskripsi Produk">
                                                    {{ $product->description }}</x-textarea>
                                                <x-button-save title="Simpan" icon="save" class="btn btn-primary" />
                                            </form>
                                        </x-modal>
                                    @endcan
                                    @can('delete-product')
                                        <x-button-delete :id="$product->id" :url="route('admin.product.destroy', $product->id)" title=""
                                            class="btn btn-danger btn-sm" />
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">Data barang tidak ditemukan.</td>
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