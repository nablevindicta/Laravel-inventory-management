@extends('layouts.master', ['title' => 'Supplier'])

@section('content')
    <x-container>
        <div class="col-12 col-lg-8">

            {{-- ✅ Card Daftar Supplier --}}
            <div class="card shadow-sm mb-4">
                <!-- Header -->
                <div class="card-header bg-white border-bottom d-flex align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-box-seam text-primary me-2"></i>
                        <strong>DAFTAR SUPPLIER</strong>
                    </div>
                </div>

                <!-- Body: Pencarian + Tabel -->
                <div class="card-body">

                    {{-- ✅ FORM PENCARIAN --}}
                    <form action="{{ route('admin.supplier.index') }}" method="GET" id="searchForm" class="mb-3">
                        <div class="row">
                            <div class="col-md-6 offset-md-6">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Cari:</span>
                                    <input 
                                        type="text" 
                                        name="search" 
                                        class="form-control" 
                                        placeholder="Cari supplier..."
                                        value="{{ $search ?? '' }}" 
                                        id="searchInput"
                                        autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </form>

                    {{-- ✅ TABEL --}}
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-3">
                            <thead class="text-center">
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 20%;">Nama Supplier</th>
                                    <th style="width: 15%;">No Hp</th>
                                    <th>Alamat</th> {{-- Kita biarkan kolom ini tanpa lebar agar fleksibel --}}
                                    <th style="width: 15%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($suppliers as $i => $supplier)
                                    <tr>
                                        <td class="text-center">{{ $i + $suppliers->firstItem() }}</td>
                                        <td>{{ $supplier->name }}</td>
                                        <td>{{ $supplier->telp }}</td>
                                        <td>{{ $supplier->address }}</td>
                                        <td>
                                            @can('update-supplier')
                                                <x-button-modal :id="$supplier->id" title="" icon="edit" style=""
                                                    class="btn btn-info btn-sm" />
                                                <x-modal :id="$supplier->id" title="Edit - {{ $supplier->name }}">
                                                    <form action="{{ route('admin.supplier.update', $supplier->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <x-input name="name" type="text" title="Nama Supplier"
                                                            placeholder="Nama Supplier" :value="$supplier->name" />
                                                        <x-input name="telp" type="text" title="Telp Supplier"
                                                            placeholder="Telp Supplier" :value="$supplier->telp" />
                                                        <x-input name="address" type="text" title="Alamat Supplier"
                                                            placeholder="Alamat Supplier" :value="$supplier->address" />
                                                        <x-button-save title="Simpan" icon="save" class="btn btn-primary" />
                                                    </form>
                                                </x-modal>
                                            @endcan
                                            @can('delete-supplier')
                                                <x-button-delete :id="$supplier->id" :url="route('admin.supplier.destroy', $supplier->id)" title=""
                                                    class="btn btn-danger btn-sm" />
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end mt-3">
                {{ $suppliers->links() }}
            </div>

        </div>

        {{-- ✅ Form Tambah Supplier — Sekarang pakai desain card yang sama --}}
        @can('create-supplier')
            <div class="col-12 col-lg-4">
                <div class="card shadow-sm mb-4">
                    <!-- Header -->
                    <div class="card-header bg-white border-bottom d-flex align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-box-seam text-primary me-2"></i>
                            <strong>TAMBAH SUPPLIER</strong>
                        </div>
                    </div>

                    <!-- Body: Form -->
                    <div class="card-body">
                        <form action="{{ route('admin.supplier.store') }}" method="POST">
                            @csrf
                            <x-input name="name" type="text" title="Nama Supplier" placeholder="Nama Supplier"
                                :value="old('name')" />
                            <x-input name="telp" type="text" title="Telp Supplier" placeholder="Telp Supplier"
                                :value="old('telp')" />
                            <x-input name="address" type="text" title="Alamat Supplier" placeholder="Alamat Supplier"
                                :value="old('address')" />
                            <x-button-save title="Simpan" icon="save" class="btn btn-primary" />
                        </form>
                    </div>
                </div>
            </div>
        @endcan
    </x-container>
@endsection