@extends('layouts.master', ['title' => 'Barang Masuk'])

@section('content')
    <x-container>
        <div class="col-12">
            <x-card title="DAFTAR BARANG MASUK" class="card-body p-0">
                <x-table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Foto</th>
                            <th>Nama Barang</th>
                            <th>Kategori Barang</th>
                            <th>Kuantitas</th>
                            <th>Satuan Barang</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $transaction)
                            <tr>
                                <td data-timestamp="{{ $transaction->created_at->toISOString() }}">
                                    {{ $transaction->created_at->format('d-m-Y H:i') }}
                                </td>
                                    @foreach ($transaction->details as $details)
                                        <div class="mb-2">
                                            <span class="avatar rounded avatar-md" style="background-image: url({{ $details->product->image }})"></span>
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach ($transaction->details as $details)
                                        {{ $details->product->name }}
                                    @endforeach
                                </td>
                                <td>
                                    @foreach ($transaction->details as $details)
                                        {{ $details->product->category->name }}
                                    @endforeach
                                </td>
                                <td>
                                    @foreach ($transaction->details as $details)
                                        {{ $details->quantity }}
                                    @endforeach
                                </td>
                                <td>
                                    @foreach ($transaction->details as $details)
                                        {{ $details->product->unit }}
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="5" class="font-weight-bold text-uppercase">
                                Total Barang Masuk
                            </td>
                            <td class="font-weight-bold text-success text-right">
                                {{ $grandQuantity }} Barang
                            </td>
                        </tr>
                    </tbody>
                </x-table>
            </x-card>
            <div class="d-flex justify-content-end">{{ $transactions->links() }}</div>
        </div>
    </x-container>

    <!-- Script untuk konversi waktu ke lokal pengguna -->
    @push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Ambil semua <td> yang memiliki data-timestamp
            document.querySelectorAll('td[data-timestamp]').forEach(function (td) {
                const isoTime = td.getAttribute('data-timestamp');
                const date = new Date(isoTime);

                // Format tanggal sesuai lokal pengguna (Indonesia)
                const options = {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                };

                // Ubah teks menjadi waktu lokal
                td.textContent = date.toLocaleString('id-ID', options);
            });
        });
    </script>
    @endpush
@endsection