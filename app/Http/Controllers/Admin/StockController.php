<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;

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

        if ($add === 0 && $reduce === 0) {
            return back()->withErrors([
                'add_stock' => 'Silakan isi salah satu: tambah stok atau kurangi stok.'
            ])->withInput();
        }

        if ($add > 0 && $reduce > 0) {
            return back()->withErrors([
                'add_stock' => 'Tidak bisa menambah dan mengurangi stok dalam satu waktu.'
            ])->withInput();
        }

        // Gunakan DB Transaction untuk memastikan kedua operasi berhasil atau gagal bersamaan
        DB::transaction(function () use ($product, $add, $reduce) {
            // Jika ada pengurangan stok, catat sebagai 'barang keluar'
            if ($reduce > 0) {
                // Buat catatan utama (header) barang keluar
                $transaction = Transaction::create([
                    'user_id' => auth()->id(), // Mencatat user yang melakukan aksi ini
                    'transaction_date' => now(),
                    // 'type' => 'out', // Tambahkan kolom 'type' di tabel transactions
                ]);

                // Catat detail barang yang keluar
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'quantity' => $reduce,
                ]);
            }
            
            // Hitung stok baru dan simpan ke database
            $newQuantity = $product->quantity + $add - $reduce;
            $product->quantity = $newQuantity;
            $product->save();
        });

        if ($add > 0) {
            $message = "Stok berhasil ditambahkan sebanyak {$add}.";
        } else {
            $message = "Stok berhasil dikurangi sebanyak {$reduce}.";
        }

        return back()->with('toast_success', $message);
    }
}