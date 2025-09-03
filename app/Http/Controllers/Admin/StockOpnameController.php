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
        $selectedMonth = $request->input('month', now()->month);
        $selectedYear = $request->input('year', now()->year);

        $sessions = StockOpnameSession::with('logs.product')
            ->where('opname_year', $selectedYear)
            ->where('opname_month', $selectedMonth)
            ->latest()
            ->paginate(10)
            ->appends($request->except('page'));

        $products = Product::with('category', 'supplier')->get();

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