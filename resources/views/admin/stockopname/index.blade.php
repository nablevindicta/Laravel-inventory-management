@extends('layouts.master', ['title' => 'Log Stok Opname'])

@section('content')
    <x-container>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5"></h2>
            <div>
                {{-- Tombol "Mulai Stok Opname" diubah menjadi modal --}}
                <x-button-modal id="create-opname-modal" title="Mulai Stok Opname" icon="plus" 
                    class="btn btn-primary mr-2" style="" />
                <a href="{{ route('admin.stockopname.pdf', ['month' => request('month', now()->month), 'year' => request('year', now()->year)]) }}" class="btn btn-success mr-2"> <i class="fas fa-file-pdf"></i> Export PDF</a>
            </div>
        </div>

        <x-card title="Filter Data" class="card-body mb-3">
            <form action="{{ route('admin.stockopname.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="month" class="form-label">Bulan</label>
                        <select name="month" id="month" class="form-select">
                            <option value="">Pilih Bulan</option> 
                            @foreach (range(1, 12) as $month)
                                <option value="{{ $month }}" {{ request('month') == $month ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($month)->monthName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="year" class="form-label">Tahun</label>
                        <select name="year" id="year" class="form-select">
                            <option value="">Pilih Tahun</option>
                            @foreach (range(now()->year, 2020) as $year)
                                <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.stockopname.index') }}" class="btn btn-secondary w-100">Reset</a>
                    </div>
                </div>
            </form>
        </x-card>

        {{-- ✅ INI BAGIAN YANG DIUBAH — TABEL UTAMA LOG STOK OPNAME --}}
        <div class="card shadow-sm mb-4">
            <!-- Header -->
            <div class="card-header bg-white border-bottom d-flex align-items-center">
                <div class="d-flex align-items-center">
                    <i class="bi bi-box-seam text-primary me-2"></i>
                    <strong>DAFTAR SESI STOK OPNAME</strong>
                </div>
                <!-- Tidak ada tombol tambah di header tabel ini -->
            </div>

            <!-- Body: Tabel + Pagination -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-3">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul Sesi</th>
                                <th>Tanggal Dibuat</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sessions as $i => $session)
                                <tr>
                                    <td>{{ $sessions->firstItem() + $i }}</td>
                                    <td>{{ $session->title }}</td>
                                    <td>{{ $session->created_at->format('d-m-Y H:i') }}</td>
                                    <td class="text-center">
                                        <x-button-modal :id="'detail-modal-' . $session->id" title="Detail" icon="eye" style=""
                                            class="btn btn-primary btn-sm" />
                                        @can('delete-stockopname')
                                        <x-button-delete :id="$session->id" :url="route('admin.stockopname.destroy', $session->id)"
                                            class="btn btn-danger btn-sm" title=""/>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada sesi stok opname.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end mt-3">{{ $sessions->links() }}</div>
    </x-container>
    
    {{-- KUMPULAN MODAL DETAIL DIPINDAHKAN KE SINI (DI LUAR TABEL) --}}
    @foreach ($sessions as $session)
    <x-modal :id="'detail-modal-' . $session->id" title="Detail Log - {{ $session->title }}">
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('admin.stockopname.pdf_detail', $session->id) }}" class="btn btn-success btn-sm" target="_blank">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </div>
        <x-table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Stok Sistem</th>
                    <th>Stok Fisik</th>
                    <th>Selisih</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($session->logs as $log)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ optional($log->product)->code }}</td>
                        <td>{{ optional($log->product)->name }}</td>
                        <td>{{ $log->stock_sistem }}</td>
                        <td>{{ $log->stock_fisik }}</td>
                        <td>{{ $log->selisih }}</td>
                        <td>
                            @if ($log->keterangan === 'Kelebihan')
                                <span class="badge bg-success">{{ $log->keterangan }}</span>
                            @elseif ($log->keterangan === 'Kekurangan')
                                <span class="badge bg-danger">{{ $log->keterangan }}</span>
                            @else
                                <span class="badge bg-secondary">{{ $log->keterangan }}</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
    </x-modal>
    @endforeach

    {{-- Modal untuk membuat stok opname baru (posisinya sudah benar) --}}
    <x-modal id="create-opname-modal" title="Mulai Stok Opname">
        <form action="{{ route('admin.stockopname.store') }}" method="POST">
            @csrf
            {{-- Dropdown untuk Bulan dan Tahun Opname --}}
            <div class="row mb-3">
                <div class="col-6">
                    <label for="opname_month" class="form-label">Bulan Opname</label>
                    <select name="opname_month" id="opname_month" class="form-select">
                        @foreach (range(1, 12) as $month)
                            <option value="{{ $month }}" {{ now()->month == $month ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($month)->monthName }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6">
                    <label for="opname_year" class="form-label">Tahun Opname</label>
                    <select name="opname_year" id="opname_year" class="form-select">
                        @foreach (range(now()->year, 2020) as $year)
                            <option value="{{ $year }}" {{ now()->year == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            {{-- Tabel Daftar Barang untuk Stok Fisik --}}
            <x-table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Stok Sistem</th>
                        <th>Stok Fisik</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $i => $product)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ optional($product->category)->name }}</td>
                        <td>{{ $product->quantity }}</td>
                        <td>
                            <input
                                type="number"
                                name="stock_fisik[{{ $product->id }}]"
                                value="{{ old('stock_fisik.' . $product->id, $product->quantity) }}"
                                class="form-control @error('stock_fisik.' . $product->id) is-invalid @enderror"
                                min="0"
                                required
                            >
                            @error('stock_fisik.' . $product->id)
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </x-table>
            <div class="mt-4 p-4">
                <button type="submit" class="btn btn-primary">Simpan Stok Opname</button>
            </div>
        </form>
    </x-modal>
@endsection