@extends('layouts.master', ['title' => 'Form Stok Opname'])

@section('content')
    <x-container>
        {{-- Menambahkan title pada komponen x-card --}}
        <x-card title="FORM STOK OPNAME" class="card-body p-0">
            <form action="{{ route('admin.stockopname.store') }}" method="POST">
                @csrf
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
                    <a href="{{ route('admin.stockopname.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </x-card>
    </x-container>
@endsection