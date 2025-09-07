<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        $categories = Category::all(); // atau Category::count() jika hanya butuh angka

        return view('admin.stock.index', compact('products', 'search', 'categories'));
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
        'corrected_stock' => ['nullable', 'integer', 'min:0'],
    'description' => ['nullable', 'string', 'max:255'], 
    ], [
        'reduce_stock.min' => 'Jumlah yang dikurangi harus 0 atau lebih.',
        'add_stock.min' => 'Jumlah tambahan stok harus 0 atau lebih.',
        'corrected_stock.min' => 'Stok koreksi tidak boleh negatif.',
    ]);

    $add = (int) $request->add_stock;
    $reduce = (int) $request->reduce_stock;
    $corrected = $request->corrected_stock;

    $filled = 0;
    $filled += $add > 0 ? 1 : 0;
    $filled += $reduce > 0 ? 1 : 0;
    $filled += $corrected !== null ? 1 : 0;

    if ($filled === 0) {
        return back()->withErrors([
            'error' => 'Silakan isi salah satu: tambah, kurangi, atau koreksi stok.'
        ])->withInput();
    }

    if ($filled > 1) {
        return back()->withErrors([
            'error' => 'Hanya boleh menggunakan satu metode perubahan stok.'
        ])->withInput();
    }

    try {
        DB::transaction(function () use ($product, $add, $reduce, $corrected, $request) { // Tambahkan $request
            if ($corrected !== null) {
                $product->quantity = $corrected;
                $product->save();
                return;
            }

            if ($add > 0 && $reduce > 0) {
                throw new \Exception('Tidak bisa tambah dan kurang sekaligus.');
            }

            if ($reduce > $product->quantity) {
                throw new \Exception("Stok tidak cukup. Tersedia: {$product->quantity}");
            }

            if ($add > 0) {
                $transaction = Transaction::create([
                    'user_id' => auth()->id(),
                    'transaction_date' => now(),
                    'type' => 'in',
                    'description' => $request->description, 
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
                    // DESKRIPSI DISIMPAN DI SINI
                    'description' => $request->description, 
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

        if ($corrected !== null) {
            return back()->with('toast_success', "Stok berhasil dikoreksi menjadi {$corrected}.");
        }

        return back()->with('toast_success', $add > 0
            ? "Stok berhasil ditambahkan sebanyak {$add}."
            : "Stok berhasil dikurangi sebanyak {$reduce}.");

    } catch (\Exception $e) {
        Log::error('Stock update failed: ' . $e->getMessage());
        return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
    }
}
}