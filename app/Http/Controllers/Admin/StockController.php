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
        // 1. Ambil semua input filter dari request
        $search = $request->input('search');
        $filterCategory = $request->input('category');
        $filterYear = $request->input('year');

        // Mengambil data untuk dropdown filter dan modal
        $categories = Category::orderBy('name')->get();
        
        // Ambil daftar tahun unik dari data produk untuk dropdown filter tahun
        $years = Product::selectRaw('YEAR(registered_at) as year')
                    ->whereNotNull('registered_at')
                    ->distinct()
                    ->orderBy('year', 'desc')
                    ->pluck('year');

        // 2. Modifikasi query untuk menerapkan semua filter
        $products = Product::with('category') 
            ->when($search, function ($query, $keyword) {
                // Filter berdasarkan kata kunci pencarian
                return $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', '%' . $keyword . '%')
                      ->orWhere('code', 'like', '%' . $keyword . '%');
                });
            })
            ->when($filterCategory, function ($query, $categoryId) {
                // Filter berdasarkan kategori
                return $query->where('category_id', $categoryId);
            })
            ->when($filterYear, function ($query, $year) {
                // Filter berdasarkan tahun registrasi
                return $query->whereYear('registered_at', $year);
            })
            ->latest()
            ->paginate(10)
            ->appends($request->except('page'));

        // 3. Kirim semua data yang dibutuhkan ke view
        return view('admin.stock.index', compact(
            'products', 
            'search', 
            'categories', 
            'years',
            'filterCategory',
            'filterYear'
        ));
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
        'transaction_date' => ['required', 'date'], 

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
        DB::transaction(function () use ($product, $add, $reduce, $corrected, $request) { 

            $transactionDate = $request->input('transaction_date');

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
                    'transaction_date' => $transactionDate,
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
                    'transaction_date' => $transactionDate,
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