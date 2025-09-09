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
use Illuminate\Support\Facades\DB;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    use HasImage;

    /**
     * Menampilkan daftar semua produk.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // 1. Ambil input pencarian
        $search = $request->input('search');

        // Mengambil data untuk modal (kategori dan supplier)
        $categories = Category::all();
        $suppliers = Supplier::all();

        // 2. Query DIUBAH menggunakan gaya when() seperti di SupplierController
        $products = Product::with(['category', 'supplier'])
            ->when($search, function ($query, $keyword) {
                // Logika pencarian canggih Anda tetap dipertahankan di sini
                return $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', '%' . $keyword . '%')
                      ->orWhere('code', 'like', '%' . $keyword . '%')
                      ->orWhereHas('supplier', function ($subQ) use ($keyword) {
                          $subQ->where('name', 'like', '%' . $keyword . '%');
                      })
                      ->orWhereHas('category', function ($subQ) use ($keyword) {
                          $subQ->where('name', 'like', '%' . $keyword . '%');
                      });
                });
            })
            ->latest()
            ->paginate(10)
            ->appends($request->query()); // 3. Menggunakan withQueryString() agar sama persis

        // 4. Kirim data ke view
        return view('admin.product.index', compact('products', 'search', 'categories', 'suppliers'));
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

        // Ambil data kategori yang dipilih
        $category = Category::findOrFail($request->category_id);
        
        // Hitung jumlah produk yang sudah ada di kategori ini
        $productCount = Product::where('category_id', $category->id)->count();
        $newNumber = $productCount + 1;

        // Ambil 3 huruf pertama dari nama kategori
        $categoryCode = strtoupper(substr($category->name, 0, 15));
        
        // Buat kode barang otomatis
        $productCode = "{$categoryCode} / 1.1.7.{$newNumber}";

        Product::create([
            'category_id' => $request->category_id,
            'supplier_id' => $request->supplier_id,
            'name' => $request->name,
            'image' => $image ? $image->hashName() : null,
            'unit' => $request->unit,
            'description' => $request->description,
            'quantity' => 0,
            'code' => $productCode,
        ]);

        return redirect(route('admin.product.index'))->with('toast_success', 'Barang berhasil ditambahkan');
    }
    
    // ... sisa method (update, destroy, dll) tidak perlu diubah ...

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
        // Langkah 1: Cek kuantitas (stok) barang.
        // Jika stok LEBIH DARI 0, batalkan proses.
        if ($product->quantity > 0) {
            // Langsung hentikan fungsi dan kembali ke halaman sebelumnya
            // dengan membawa pesan error.
            return back()->with('toast_error', 'Gagal! Barang tidak dapat dihapus karena stok masih tersedia.');
        }

        // Langkah 2: Jika kode berlanjut ke sini, artinya stok adalah 0.
        // Lanjutkan proses hapus permanen.
        try {
            DB::transaction(function () use ($product) {
                // Hapus detail transaksi yang terkait (jika ada)
                TransactionDetail::where('product_id', $product->id)->delete();
                
                // Hapus file gambar dari penyimpanan jika ada
                if ($product->getOriginal('image')) {
                    Storage::disk('local')->delete('public/products/' . $product->getOriginal('image'));
                }

                // Hapus data secara permanen dari database
                $product->forceDelete();
            });

            return back()->with('toast_success', 'Barang berhasil dihapus secara permanen.');

        } catch (\Exception $e) {
            Log::error('Gagal menghapus permanen produk: ' . $e->getMessage());
            return back()->with('toast_error', 'Terjadi kesalahan pada server saat mencoba menghapus barang.');
        }
    }
}