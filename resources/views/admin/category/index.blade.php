@extends('layouts.master', ['title' => 'Kategori'])

@section('content')
    <x-container>
        <div class="col-12 col-lg-8">

            {{-- ✅ Card Daftar Kategori --}}
            <div class="card shadow-sm mb-4">
                <!-- Header -->
                <div class="card-header bg-white border-bottom d-flex align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-box-seam text-primary me-2"></i>
                        <strong>DAFTAR KATEGORI</strong>
                    </div>
                </div>

                <!-- Body: Pencarian + Tabel -->
                <div class="card-body">

                    {{-- ✅ FORM PENCARIAN --}}
                    <form action="{{ route('admin.category.index') }}" method="GET" id="searchForm" class="mb-3">
                        <div class="row">
                            <div class="col-md-6 offset-md-6">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Cari:</span>
                                    <input 
                                        type="text" 
                                        name="search" 
                                        class="form-control" 
                                        placeholder="Cari kategori..."
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
                                    <th>No</th>
                                    <th>Foto</th>
                                    <th>Nama Kategori</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $i => $category)
                                    <tr>
                                        <td class="text-center">{{ $i + $categories->firstItem() }}</td>
                                        <td class="text-center">
                                            <span class="avatar rounded avatar-md"
                                                style="background-image: url({{ $category->image }})"></span>
                                        </td>
                                        <td>{{ $category->name }}</td>
                                        <td class="text-center">
                                            @can('update-category')
                                                <x-button-modal :id="$category->id" title="" icon="edit" style=""
                                                    class="btn btn-info btn-sm" />
                                                <x-modal :id="$category->id" title="Edit - {{ $category->name }}">
                                                    <form action="{{ route('admin.category.update', $category->id) }}" method="POST"
                                                        enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <x-input name="name" type="text" title="Nama Kategori"
                                                            placeholder="Nama Kategori" :value="$category->name" />
                                                        <x-input name="image" type="file" title="Foto Kategori" placeholder=""
                                                            :value="$category->image" />
                                                        <x-button-save title="Simpan" icon="save" class="btn btn-primary" />
                                                    </form>
                                                </x-modal>
                                            @endcan
                                            @can('delete-category')
                                                <x-button-delete :id="$category->id" :url="route('admin.category.destroy', $category->id)" title=""
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

            {{-- Pagination --}}
            <div class="d-flex justify-content-end">
                {{ $categories->links() }}
            </div>
        </div>

        {{-- ✅ Form Tambah Kategori — Sekarang pakai desain card yang sama --}}
        @can('create-category')
            <div class="col-12 col-lg-4">
                <div class="card shadow-sm mb-4">
                    <!-- Header -->
                    <div class="card-header bg-white border-bottom d-flex align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-box-seam text-primary me-2"></i>
                            <strong>TAMBAH KATEGORI</strong>
                        </div>
                    </div>

                    <!-- Body: Form -->
                    <div class="card-body">
                        <form action="{{ route('admin.category.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <x-input name="name" type="text" title="Nama Kategori" placeholder="Nama Kategori"
                                :value="old('name')" />
                            <x-input name="image" type="file" title="Foto Kategori" placeholder="" :value="old('image')" />
                            <x-button-save title="Simpan" icon="save" class="btn btn-primary" />
                        </form>
                    </div>
                </div>
            </div>
        @endcan
    </x-container>
@endsection