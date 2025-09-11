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
    // app/Http/Controllers/Admin/ProductController.php

    public function index(Request $request)
    {
        // 1. Ambil semua input filter dari request
        $search = $request->input('search');
        $filterCategory = $request->input('category');
        $filterYear = $request->input('year');

        // Mengambil data untuk dropdown filter dan modal
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        
        // DITAMBAHKAN: Ambil daftar tahun unik dari data produk untuk dropdown filter tahun
        $years = Product::selectRaw('YEAR(registered_at) as year')
                    ->whereNotNull('registered_at')
                    ->distinct()
                    ->orderBy('year', 'desc')
                    ->pluck('year');

        // 2. Modifikasi query untuk menerapkan semua filter
        $products = Product::with(['category', 'supplier'])
            ->when($search, function ($query, $keyword) {
                // Filter berdasarkan kata kunci pencarian
                return $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('code', 'like', '%' . $keyword . '%');
                });
            })
            ->when($filterCategory, function ($query, $categoryId) {
                // DITAMBAHKAN: Filter berdasarkan kategori
                return $query->where('category_id', $categoryId);
            })
            ->when($filterYear, function ($query, $year) {
                // DITAMBAHKAN: Filter berdasarkan tahun registrasi
                return $query->whereYear('registered_at', $year);
            })
            ->latest()
            ->paginate(10)
            ->appends($request->except('page'));

        // 3. Kirim semua data yang dibutuhkan ke view
        return view('admin.product.index', compact(
            'products', 
            'search', 
            'categories', 
            'suppliers',
            'years', // DITAMBAHKAN
            'filterCategory', // DITAMBAHKAN
            'filterYear' // DITAMBAHKAN
        ));
    }

    /**
     * Menyimpan produk baru ke dalam penyimpanan.
     *
     * @param  \App\Http\Requests\ProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    // Di dalam file app/Http/Controllers/Admin/ProductController.php

    public function store(ProductRequest $request)
    {
        // 1. Ambil semua data yang sudah lolos validasi dari ProductRequest
        $data = $request->validated();

        // 2. Logika untuk upload gambar (jika ada)
        if ($request->hasFile('image')) {
            // Gunakan trait HasImage yang sudah Anda miliki
            $image = $this->uploadImage($request, 'public/products/', 'image');
            $data['image'] = $image->hashName();
        }

        // 3. Logika untuk membuat kode produk otomatis
        $category = Category::findOrFail($data['category_id']);
        $productCount = Product::where('category_id', $category->id)->count();
        $newNumber = $productCount + 1;
        $categoryCode = strtoupper(substr($category->name, 0, 15));
        $productCode = "{$categoryCode} / 1.1.7.{$newNumber}";
        
        $data['code'] = $productCode;

        // 4. Set stok awal menjadi 0
        $data['quantity'] = 0;

        // 5. Simpan ke database menggunakan data yang sudah bersih dan lengkap
        Product::create($data);

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
            'registered_at' => $request->registered_at,
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