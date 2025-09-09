<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\StockOpnameLog;
use App\Models\StockOpnameSession;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class StockOpnameController extends Controller
{
    /**
     * Menampilkan daftar log stok opname dengan filter bulan dan tahun.
     */
    public function index(Request $request)
    {
        // 1. Hapus nilai default agar menjadi null saat tidak ada filter
        $selectedMonth = $request->input('month');
        $selectedYear = $request->input('year');

        // 2. Mulai query builder dasar
        $query = StockOpnameSession::with('logs.product');

        // 3. Terapkan filter HANYA JIKA bulan dan tahun dipilih oleh pengguna
        if ($selectedMonth && $selectedYear) {
            $query->where('opname_year', $selectedYear)
                  ->where('opname_month', $selectedMonth);
        }

        // 4. Lanjutkan query dengan ordering dan paginasi
        $sessions = $query->latest()
            ->paginate(10)
            ->appends($request->except('page'));

        $products = Product::with('category', 'supplier')->get();

        // Variabel selectedMonth & selectedYear tetap dikirim ke view
        // agar dropdown menampilkan nilai yang dipilih setelah filter dijalankan
        return view('admin.stockopname.index', compact('sessions', 'selectedMonth', 'selectedYear', 'products'));
    }

    /**
     * Memperbarui stok produk dan mencatat log stok opname.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'opname_month' => 'required|integer|min:1|max:12',
            'opname_year' => 'required|integer|min:2000',
            'stock_fisik.*' => 'required|numeric|min:0',
        ]);
        
        // ✅ Pindahkan redirect ke luar closure
        try {
            DB::transaction(function () use ($validated, $request) {
                $session = StockOpnameSession::create([
                    'opname_month' => $validated['opname_month'],
                    'opname_year' => $validated['opname_year'],
                    'title' => 'Stok Opname ' . \Carbon\Carbon::create()->month($validated['opname_month'])->monthName . ' ' . $validated['opname_year'],
                ]);

                foreach ($validated['stock_fisik'] as $productId => $newStock) {
                    $product = Product::findOrFail($productId);
                    
                    $oldStock = $product->quantity;
                    $selisih = $newStock - $oldStock;

                    if ($selisih > 0) {
                        $keterangan = 'Kelebihan';
                    } elseif ($selisih < 0) {
                        $keterangan = 'Kekurangan';
                    } else {
                        $keterangan = 'Sesuai';
                    }

                    $product->quantity = $newStock;
                    $product->save();

                    StockOpnameLog::create([
                        'product_id' => $productId,
                        'stock_sistem' => $oldStock,
                        'stock_fisik' => $newStock,
                        'selisih' => $selisih,
                        'keterangan' => $keterangan,
                        'stock_opname_session_id' => $session->id,
                    ]);
                }
            });

            // ✅ Redirect sekarang berada di luar transaction
            return redirect()->route('admin.stockopname.index', [
                'month' => $validated['opname_month'],
                'year' => $validated['opname_year']
            ])->with('toast_success', 'Stok Opname berhasil disimpan!');

        } catch (\Exception $e) {
            Log::error('Stock Opname failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function destroy(StockOpnameSession $stockOpnameSession)
    {
        // (Saran Performa) Muat relasi logs dan product di awal untuk efisiensi
        $stockOpnameSession->load('logs.product');

        // Gunakan transaction untuk memastikan semua proses aman
        DB::transaction(function () use ($stockOpnameSession) {
            
            // LANGKAH 1: KEMBALIKAN STOK SEMUA PRODUK
            // Loop melalui setiap log di dalam sesi yang akan dihapus
            foreach ($stockOpnameSession->logs as $log) {
                
                // Cek jika produk dari log ini masih ada di database
                if ($log->product) {
                    
                    // Kembalikan 'quantity' produk ke nilai 'stock_sistem'
                    // yang tercatat saat opname dilakukan (nilai sebelum diubah).
                    $log->product->update(['quantity' => $log->stock_sistem]);
                }
            }

            // LANGKAH 2: HAPUS LOG DAN SESI
            // Hapus semua record log dari sesi ini
            $stockOpnameSession->logs()->delete();

            // Setelah stok dikembalikan dan log dihapus, hapus sesi utamanya
            $stockOpnameSession->delete();
        });

        // Berikan pesan yang jelas kepada pengguna
        return back()->with('toast_success', 'Sesi stok opname berhasil dihapus dan stok barang telah dikembalikan.');
    }


    /**
     * Mengekspor log stok opname ke PDF dengan filter bulan dan tahun.
     */
    public function exportPdf(Request $request)
    {
        $selectedMonth = $request->input('month');
        $selectedYear = $request->input('year');

        $logs = StockOpnameLog::with('product')
            ->whereYear('created_at', $selectedYear)
            ->whereMonth('created_at', $selectedMonth)
            ->latest()
            ->get();
        
        $title = 'Laporan Stok Opname';
        $fileName = str_replace(' ', '_', strtolower($title)) . '_' . now()->format('Y-m-d_H-i-s') . '.pdf';

        $pdf = PDF::loadView('admin.stockopname.report.pdf', compact('logs', 'title'));
        return $pdf->download($fileName);
    }

    public function exportDetailPdf(StockOpnameSession $stockOpnameSession)
    {
        // 1. Eager load relasi untuk menghindari N+1 problem di view PDF
        $stockOpnameSession->load('logs.product');

        // 2. Siapkan data untuk dikirim ke view PDF
        $data = [
            'session' => $stockOpnameSession
        ];

        // 3. Buat nama file yang dinamis
        $fileName = 'Laporan Opname - ' . $stockOpnameSession->title . '.pdf';

        // 4. Load view PDF dengan data, dan generate PDF untuk diunduh
        $pdf = PDF::loadView('admin.stockopname.report.pdf_detail', $data);
        
        return $pdf->download($fileName);
    }
}