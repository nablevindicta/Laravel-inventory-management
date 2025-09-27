<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Traits\HasImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Product; 
use App\Models\TransactionDetail; 
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    use HasImage;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->search;

        $categories = Category::when($search, function($query) use($search){
            $query = $query->where('name', 'like', '%'.$search.'%');
        })->paginate(10)->withQueryString();

        return view('admin.category.index', compact('categories', 'search'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request)
    {
        $image = $this->uploadImage($request, $path = 'public/categories/', $name = 'image');

        Category::create([
            'name' => $request->name,
            'image' => $image->hashName(),
        ]);

        return back()->with('toast_success', 'Kategori Berhasil Ditambahkan');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $image = $this->uploadImage($request, $path = 'public/categories/', $name = 'image');

        $category->update([
            'name' => $request->name,
        ]);

        if($request->file($name)){
            $this->updateImage(
                $path = 'public/categories/', $name = 'image', $data = $category, $url = $image->hashName()
            );
        }

        return back()->with('toast_success', 'Kategori Berhasil Diubah');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        // 1. Cek apakah ada produk yang menggunakan kategori ini
        if ($category->products()->count() > 0) {
            // 2. Jika ada, batalkan penghapusan dan kirim pesan error
            return back()->with('toast_error', 'Kategori tidak dapat dihapus karena masih digunakan oleh beberapa produk.');
        }

        // 3. Jika tidak ada produk terkait, lanjutkan proses penghapusan
        try {
            // Hapus gambar kategori dari storage jika ada
            if ($category->image) {
                Storage::disk('public')->delete('categories/' . $category->image);
            }

            // Hapus data kategori dari database
            $category->delete();

            return back()->with('toast_success', 'Kategori berhasil dihapus.');

        } catch (\Exception $e) {
            // Catat error untuk debugging
            Log::error('Gagal menghapus kategori: ' . $e->getMessage());
            return back()->with('toast_error', 'Terjadi kesalahan saat menghapus kategori.');
        }
    }
}
