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
    
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
    
        $quantity = $request->input('quantity');
    
        if ($request->input('action') === 'reduce') {
            if ($quantity > $product->quantity) {
                return back()->with('toast_error', 'Jumlah melebihi stok tersedia.');
            }
            $product->quantity -= $quantity;
        } else {
            // Default: replace stok langsung
            $product->quantity = $quantity;
        }
    
        $product->save();
    
        $message = $request->input('action') === 'reduce' 
            ? "Stok berhasil dikurangi sebanyak {$quantity}." 
            : "Stok berhasil diperbarui.";
    
        return back()->with('toast_success', $message);
    }
}