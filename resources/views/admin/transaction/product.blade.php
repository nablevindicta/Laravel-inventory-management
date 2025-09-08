@extends('layouts.master', ['title' => $type === 'in' ? 'Barang Masuk' : 'Barang Keluar'])

@section('content')
    <x-container>
        <div class="col-12">

            <form action="{{ route('admin.transaction.product') }}" method="GET" class="mb-4">
                <input type="hidden" name="type" value="{{ $type }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date', $startDate) }}">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">Tanggal Selesai</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date', $endDate) }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div> 
                    <div class="col-md-2">
                        <a href="{{ route('admin.transaction.pdf', ['type' => $type] + request()->query()) }}" class="btn btn-success w-100 mb-2"> <i class="fas fa-file-pdf"></i> Export PDF</a>
                        <a href="{{ route('admin.transaction.product') }}?type={{ $type }}" class="btn btn-secondary w-100">Reset</a>
                    </div>
                </div>
            </form>

            @php
                $title = $type === 'in' ? 'DAFTAR BARANG MASUK' : 'DAFTAR BARANG KELUAR';
                $totalLabel = $type === 'in' ? 'Total Barang Masuk' : 'Total Barang Keluar';
                $icon = $type === 'in' ? 'shopping-cart-plus' : 'shopping-cart-minus';
            @endphp

            {{-- ✅ Card Tabel dengan Header dan Pencarian --}}
            <div class="card shadow-sm mb-4">
                <!-- Header -->
                <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-box-seam text-primary me-2"></i>
                        <strong>{{ $title }}</strong>
                    </div>
                    <!-- Tidak ada tombol tambah -->
                </div>

                <!-- Body: Pencarian + Tabel -->
                <div class="card-body">

<<<<<<< HEAD
                    {{-- ✅ FORM PENCARIAN --}}
                    <form action="{{ route('admin.transaction.product') }}" method="GET" id="searchForm" class="mb-3">
                        <input type="hidden" name="type" value="{{ $type }}">
                        @if(request('start_date'))
                            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                        @endif
                        @if(request('end_date'))
                            <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                        @endif
=======
                        <!-- Baris Total (dinamis) -->
                        <tr>
                            <td colspan="8" class="font-weight-bold text-uppercase">
                                {{ $totalLabel }}
                            </td>
                            <td class="font-weight-bold text-left">
                                <span class="text-{{ $type === 'in' ? 'success' : 'danger' }}">
                                    {{ $grandQuantity }} Barang
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </x-table>
            </x-card>
>>>>>>> 773277d0f33ec6c919a4be05f29cf15c7f208c58

                        <div class="row">
                            <div class="col-md-6 offset-md-6">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Cari:</span>
                                    <input 
                                        type="text" 
                                        name="search" 
                                        class="form-control" 
                                        placeholder="Cari barang, kategori, atau keterangan..."
                                        value="{{ request('search') }}" 
                                        id="searchInput">
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-3" id="transactionTable">
                            <thead>
                                <tr>    
                                    <th>Tanggal</th>
                                    <th>Foto</th>
                                    <th>Kode</th>
                                    <th>Nama Barang</th>
                                    <th>Kategori Barang</th>
                                    <th>Kuantitas</th>
                                    <th>Satuan Barang</th>
                                    <th>Keterangan</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $transaction)
                                    <tr>
                                        <td data-timestamp="{{ $transaction->created_at->toISOString() }}">
                                            {{ $transaction->created_at->format('d-m-Y H:i') }}
                                        </td>

                                        <td>
                                            @foreach ($transaction->details as $details)
                                                <div class="mb-2">
                                                    <span class="avatar rounded avatar-md" style="background-image: url({{ $details->product->image }})"></span>
                                                </div>
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach ($transaction->details as $details)
                                                <div>{{ $details->product->code }}</div>
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach ($transaction->details as $details)
                                                <div>{{ $details->product->name }}</div>
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach ($transaction->details as $details)
                                                <div>{{ $details->product->category->name }}</div>
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach ($transaction->details as $details)
                                                <div>{{ $details->quantity }}</div>
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach ($transaction->details as $details)
                                                <div>{{ $details->product->unit }}</div>
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach ($transaction->details as $details)
                                                <div>{{ $details->transaction->description }}</div>
                                            @endforeach 
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('admin.transaction.destroy', $transaction->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini? Stok akan dikembalikan.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach

                                <!-- Baris Total (dinamis) -->
                                <tr>
                                    <td colspan="5" class="font-weight-bold text-uppercase">
                                        {{ $totalLabel }}
                                    </td>
                                    <td class="font-weight-bold text-right">
                                        <span class="text-{{ $type === 'in' ? 'success' : 'danger' }}">
                                            {{ $grandQuantity }} Barang
                                        </span>
                                    </td>
                                    <td colspan="3"></td> <!-- Kosongkan kolom sisanya agar alignment tetap benar -->
                                </tr>
                            </tbody>
                        </table>
                    <!-- Pagination -->
                    <div class="d-flex justify-content-end">
                        {{ $transactions->appends(array_filter([
                            'type' => $type,
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                            'search' => request('search')
                        ]))->links() }}
                    </div>
                    </div>
                </div>
            </div>

            
        </div>
    </x-container>

    <!-- Script untuk konversi waktu ke lokal pengguna + Pencarian Realtime -->
    @push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Konversi waktu lokal
            const userTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            const formatOptions = {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false,
                timeZone: userTimeZone
            };

            document.querySelectorAll('td[data-timestamp]').forEach(function (td) {
                const isoTime = td.getAttribute('data-timestamp');
                const date = new Date(isoTime);

                if (isNaN(date.getTime())) {
                    console.error('Invalid date:', isoTime);
                    return;
                }

                td.textContent = new Intl.DateTimeFormat('id-ID', formatOptions).format(date);
            });

            // 2. Pencarian realtime di tabel (client-side)
            const searchInput = document.getElementById('searchInput');
            const table = document.getElementById('transactionTable');
            const tbody = table.querySelector('tbody');
            const rows = tbody.querySelectorAll('tr:not(:last-child)'); // Jangan sertakan baris total

            if (searchInput && rows.length) {
                searchInput.addEventListener('keyup', function () {
                    const searchText = searchInput.value.toLowerCase().trim();

                    rows.forEach(row => {
                        const cells = row.querySelectorAll('td');
                        let found = false;

                        cells.forEach(cell => {
                            // Abaikan kolom aksi (button) dan timestamp
                            if (cell.querySelector('form') || cell.hasAttribute('data-timestamp')) {
                                return;
                            }
                            const text = cell.textContent.toLowerCase();
                            if (text.includes(searchText)) {
                                found = true;
                            }
                        });

                        row.style.display = found ? '' : 'none';
                    });
                });
            }
        });
    </script>
    @endpush
@endsection