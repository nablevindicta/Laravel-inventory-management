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

    // Validasi jumlah stok
    $request->validate([
        'quantity' => 'required|integer|min:0',
    ]);

    $quantity = $request->input('quantity');

    // Cek apakah ini aksi pengurangan
    if ($request->filled('action') && $request->input('action') === 'reduce') {
        if ($quantity > $product->quantity) {
            return back()->with('toast_error', 'Jumlah pengurangan melebihi stok saat ini.');
        }
        $product->quantity -= $quantity;
        $product->save();
        return back()->with('toast_success', "Stok berhasil dikurangi sebanyak {$quantity}.");
    }

    // Jika bukan reduce, maka lakukan replace langsung (set stok baru)
    $product->quantity = $quantity;
    $product->save();

    return back()->with('toast_success', 'Stok berhasil diperbarui.');
}

    /**
     * Display stock report.
     */
    public function report()
    {
        $products = Product::with('supplier', 'category')->paginate(10);
        return view('admin.stock.report', compact('products'));
    }
}