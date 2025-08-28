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
    
    public function index(Request $request)
    {
        $search = $request->search;

        $products = Product::when($search, function($query) use($search){
            $query = $query->where('name', 'like', '%'.$search.'%');
        })->paginate(10)->withQueryString();

        return view('admin.stock.index', compact('products', 'search'));
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
            'current_stock' => 'nullable|integer|min:0',
            'add_stock' => 'nullable|integer|min:0',
            'reduce_stock' => 'nullable|integer|min:0|max:' . $product->quantity,
        ], [
            'reduce_stock.max' => "Jumlah yang dikurangi tidak boleh lebih dari stok saat ini ({$product->quantity}).",
            'reduce_stock.min' => 'Jumlah yang dikurangi harus 0 atau lebih.',
            'add_stock.min' => 'Jumlah tambahan stok harus 0 atau lebih.',
            'current_stock.min' => 'Stok tidak boleh negatif.',
        ]);

        $currentStock = (int) $request->current_stock;
        $add = (int) $request->add_stock;
        $reduce = (int) $request->reduce_stock;

        // Cek: pilih salah satu mode
        $modeCount = 0;
        $modeCount += $request->filled('current_stock') ? 1 : 0;
        $modeCount += $add > 0 ? 1 : 0;
        $modeCount += $reduce > 0 ? 1 : 0;

        if ($modeCount === 0) {
            return back()->withErrors([
                'add_stock' => 'Silakan isi salah satu: stok baru, tambah stok, atau kurangi stok.'
            ])->withInput();
        }

        if ($modeCount > 1) {
            return back()->withErrors([
                'add_stock' => 'Hanya boleh mengisi satu metode: stok baru, tambah, atau kurangi.'
            ])->withInput();
        }

        // Gunakan transaksi database
        DB::transaction(function () use ($product, $currentStock, $add, $reduce, $request) {
            $newQuantity = $product->quantity;

            // Mode 1: Set stok langsung (tanpa log transaksi)
            if ($request->filled('current_stock')) {
                $newQuantity = $currentStock;
            }
            // Mode 2: Tambah stok (dengan log)
            elseif ($add > 0) {
                $transaction = Transaction::create([
                    'user_id' => auth()->id(),
                    'transaction_date' => now(),
                    'type' => 'in',
                ]);

                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'quantity' => $add,
                ]);

                $newQuantity = $product->quantity + $add;
            }
            // Mode 3: Kurangi stok (dengan log)
            elseif ($reduce > 0) {
                $transaction = Transaction::create([
                    'user_id' => auth()->id(),
                    'transaction_date' => now(),
                    'type' => 'out',
                ]);

                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'quantity' => $reduce,
                ]);

                $newQuantity = $product->quantity - $reduce;
            }

            // Simpan stok baru
            $product->quantity = $newQuantity;
            $product->save();
        });

        // Pesan sukses
        if ($request->filled('current_stock')) {
            $message = "Stok berhasil diperbarui menjadi {$currentStock} (koreksi langsung).";
        } elseif ($add > 0) {
            $message = "Stok berhasil ditambahkan sebanyak {$add}.";
        } else {
            $message = "Stok berhasil dikurangi sebanyak {$reduce}.";
        }

        return back()->with('toast_success', $message);
    }
}