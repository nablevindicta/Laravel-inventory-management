<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\StockOpnameLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class StockOpnameController extends Controller
{
    /**
     * Menampilkan daftar log stok opname dengan filter tanggal.
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $logs = StockOpnameLog::with('product')
            ->when($startDate, function ($query, $startDate) {
                return $query->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                return $query->whereDate('created_at', '<=', $endDate);
            })
            ->latest()
            ->paginate(10)
            ->appends($request->except('page'));

        return view('admin.stockopname.index', compact('logs', 'startDate', 'endDate'));
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

    public function exportPdf(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $logs = StockOpnameLog::with('product')
            ->when($startDate, function ($query, $startDate) {
                return $query->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                return $query->whereDate('created_at', '<=', $endDate);
            })
            ->latest()
            ->get();

        $title = 'Laporan Stok Opname';
        $fileName = str_replace(' ', '_', strtolower($title)) . '_' . now()->format('Y-m-d_H-i-s') . '.pdf';

        $pdf = PDF::loadView('admin.stockopname.report.pdf', compact('logs', 'title'));

        return $pdf->download($fileName);
    }
}