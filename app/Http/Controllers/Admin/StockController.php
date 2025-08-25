<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::with('supplier', 'category')->paginate(10);

        return view('admin.stock.index', compact('products'));
    }

/**
 * Update the specified resource in storage.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  int  $id
 * @return \Illuminate\Http\Response
 */
public function update(Request $request, $id)
{
    $product = Product::findOrFail($id);

    // Validasi input
    $request->validate([
        'add_stock' => ['nullable', 'integer', 'min:0'],
        'reduce_stock' => ['nullable', 'integer', 'min:0', 'max:' . $product->quantity],
    ], [
        'reduce_stock.max' => "Jumlah yang dikurangi tidak boleh lebih dari stok saat ini ({$product->quantity}).",
        'reduce_stock.min' => 'Jumlah yang dikurangi harus 0 atau lebih.',
        'add_stock.min' => 'Jumlah tambahan stok harus 0 atau lebih.',
    ]);

    $add = (int) $request->add_stock;
    $reduce = (int) $request->reduce_stock;

    // Validasi: minimal salah satu harus > 0
    if ($add === 0 && $reduce === 0) {
        return back()->withErrors([
            'add_stock' => 'Silakan isi salah satu: tambah stok atau kurangi stok.'
        ])->withInput();
    }

    // Validasi: tidak boleh keduanya > 0
    if ($add > 0 && $reduce > 0) {
        return back()->withErrors([
            'add_stock' => 'Tidak bisa menambah dan mengurangi stok dalam satu waktu.'
        ])->withInput();
    }

    // Hitung stok baru
    $newQuantity = $product->quantity + $add - $reduce;
    $product->quantity = $newQuantity;
    $product->save();

    // Tentukan pesan sesuai aksi
    if ($add > 0) {
        $message = "Stok berhasil ditambahkan sebanyak {$add}.";
    } else {
        $message = "Stok berhasil dikurangi sebanyak {$reduce}.";
    }

    return back()->with('toast_success', $message);
}
}