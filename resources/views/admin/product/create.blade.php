@extends('layouts.master', ['title' => 'Produk'])

@section('content')
    <x-container>
        <div class="row">
            <div class="col-12">
                <x-card title="TAMBAH PRODUK" class="card-body">
                    <form action="{{ route('admin.product.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <x-input name="name" type="text" title="Nama Produk" placeholder="Nama Produk" :value="old('name')" />
                        <x-input name="unit" type="text" title="Satuan Produk" placeholder="Satuan Produk" :value="old('unit')" />

                        <x-select title="Supplier Produk" name="supplier_id">
                            <option value="">Silahkan Pilih</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </x-select>

                        <x-select title="Kategori Produk" name="category_id">
                            <option value="">Silahkan Pilih</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </x-select>

                        <x-input name="image" type="file" title="Foto Produk" :value="old('image')" />

                        <x-textarea name="description" title="Deskripsi Produk" placeholder="Deskripsi Produk">
                            {{ old('description') }}
                        </x-textarea>

                        <!-- 🔽 Input Stok Awal -->
                        <x-input 
                            name="quantity" 
                            type="number" 
                            min="0" 
                            title="Stok Awal Produk" 
                            placeholder="Masukkan jumlah stok awal" 
                            :value="old('quantity', 0)" 
                        />

                        <div class="mt-3">
                            <x-button-save title="Simpan" icon="save" class="btn btn-primary" />
                            <x-button-link title="Kembali" icon="arrow-left" :url="route('admin.product.index')" class="btn btn-dark" style="mr-1" />
                        </div>
                    </form>
                </x-card>
            </div>
        </div>
    </x-container>
@endsection