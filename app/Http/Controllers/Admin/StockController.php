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

    $request->validate([
        'add_stock' => ['nullable', 'integer', 'min:0'],
        'reduce_stock' => ['nullable', 'integer', 'min:0'],
    ], [
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

    // Tambahkan cek stok sebelum reduce
    if ($reduce > $product->quantity) {
        return back()->withErrors([
            'reduce_stock' => "Jumlah yang dikurangi tidak boleh lebih dari stok saat ini ({$product->quantity})."
        ])->withInput();
    }

    try {
        DB::transaction(function () use ($product, $add, $reduce) {
            if ($add > 0) {
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
            }

            if ($reduce > 0) {
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
            }

            $product->quantity = $product->quantity + $add - $reduce;
            $product->save();
        });

        return back()->with('toast_success', $add > 0 ? "Stok berhasil ditambahkan sebanyak {$add}." : "Stok berhasil dikurangi sebanyak {$reduce}.");
        } catch (\Exception $e) {
            \Log::error('Stock update failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
    }
}
}