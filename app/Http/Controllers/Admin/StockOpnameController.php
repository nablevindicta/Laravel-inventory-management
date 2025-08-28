<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\StockOpnameLog;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf; // <-- Tetap ada untuk fitur PDF

class StockOpnameController extends Controller
{
    /**
     * Menampilkan daftar log stok opname dengan filter bulan dan tahun.
     */
    public function index(Request $request)
    {
        $selectedMonth = $request->input('month', now()->month);
        $selectedYear = $request->input('year', now()->year);

        $logs = StockOpnameLog::with('product')
            ->whereYear('created_at', $selectedYear)
            ->whereMonth('created_at', $selectedMonth)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.stockopname.index', compact('logs', 'selectedMonth', 'selectedYear'));
    }

    /**
     * Menampilkan formulir untuk melakukan stok opname baru.
     */
    public function create()
    {
        $products = Product::with('category', 'supplier')->get();
        return view('admin.stockopname.create', compact('products'));
    }

    /**
     * Memperbarui stok produk dan mencatat log stok opname.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'stock_fisik.*' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
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
                ]);
            }
        });

        return redirect()->route('admin.stockopname.index')->with('toast_success', 'Stok Opname berhasil disimpan!');
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
}