<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Traits\HasImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    use HasImage;

    /**
     * Menampilkan daftar semua produk.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::paginate(10);

        return view('admin.product.index', compact('products'));
    }

    /**
     * Menampilkan formulir untuk membuat produk baru.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $suppliers = Supplier::get();
        $categories = Category::get();

        return view('admin.product.create', compact('suppliers', 'categories'));
    }

    /**
     * Menyimpan produk baru ke dalam penyimpanan.
     *
     * @param  \App\Http\Requests\ProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        // Unggah gambar hanya jika ada file yang dikirim
        $image = $request->hasFile('image') ? $this->uploadImage($request, 'public/products/', 'image') : null;

        Product::create([
            'category_id' => $request->category_id,
            'supplier_id' => $request->supplier_id,
            'name' => $request->name,
            'image' => $image ? $image->hashName() : null,
            'unit' => $request->unit,
            'description' => $request->description,
            'quantity' => 0,
        ]);

        return redirect(route('admin.product.index'))->with('toast_success', 'Barang berhasil ditambahkan');
    }

    /**
     * Menampilkan formulir untuk mengedit produk yang ditentukan.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $suppliers = Supplier::get();
        $categories = Category::get();

        return view('admin.product.edit', compact('product', 'suppliers', 'categories'));
    }

    /**
     * Memperbarui produk yang ditentukan dalam penyimpanan.
     *
     * @param  \App\Http\Requests\ProductRequest  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */


    public function update(ProductRequest $request, Product $product)
    {
        // Siapkan data untuk di-update
        $data = [
            'category_id' => $request->category_id,
            'supplier_id' => $request->supplier_id,
            'name' => $request->name,
            'unit' => $request->unit,
            'description' => $request->description,
        ];

        // Periksa apakah ada file gambar baru yang diunggah
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada (menggunakan path dari database)
            if ($product->getOriginal('image')) {
                Storage::disk('local')->delete('public/products/' . $product->getOriginal('image'));
            }
            // Unggah gambar baru dan tambahkan ke array data
            $image = $this->uploadImage($request, 'public/products/', 'image');
            $data['image'] = $image->hashName();
        }

        // Perbarui data produk
        $product->update($data);

        return redirect(route('admin.product.index'))->with('toast_success', 'Data Barang Berhasil Diubah');
    }

    /**
     * Menghapus produk yang ditentukan dari penyimpanan.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        // Hapus file gambar dari penyimpanan
        if ($product->getOriginal('image')) {
            Storage::disk('local')->delete('public/products/' . basename($product->getOriginal('image')));
        }

        // Hapus data produk dari database
        $product->delete();

        // Perbaiki pesan notifikasi.
        return back()->with('toast_success', 'Barang Berhasil Dihapus');
    }
}