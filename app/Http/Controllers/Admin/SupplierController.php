<?php

namespace App\Http\Controllers\Admin;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierRequest;
use Illuminate\Support\Facades\Log;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->search;

        $suppliers = Supplier::when($search, function($query) use($search){
            $query = $query->where('name', 'like', '%'.$search.'%');
        })->paginate(10)->withQueryString();

        return view('admin.supplier.index', compact('suppliers', 'search'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SupplierRequest $request)
    {
        Supplier::create($request->all());

        return back()->with('toast_success', 'Supplier Berhasil Ditambahkan');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $supplier->update($request->all());

        return back()->with('toast_success', 'Supplier Berhasil Diubah');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Supplier $supplier)
    {
        // 1. Cek apakah ada produk yang menggunakan supplier ini
        if ($supplier->products()->count() > 0) {
            // 2. Jika ada, batalkan penghapusan dan kirim pesan error
            return back()->with('toast_error', 'Supplier tidak dapat dihapus karena masih digunakan oleh beberapa produk.');
        }

        // 3. Jika tidak ada produk terkait, lanjutkan proses penghapusan
        try {
            $supplier->delete();
            return back()->with('toast_success', 'Supplier berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus supplier: ' . $e->getMessage());
            return back()->with('toast_error', 'Terjadi kesalahan saat menghapus supplier.');
        }
    }
}
