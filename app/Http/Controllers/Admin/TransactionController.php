<?php

namespace App\Http\Controllers\Admin;

use App\Models\Rent;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TransactionDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\PDF;

class TransactionController extends Controller
{
    /**
    * Menampilkan daftar transaksi (in/out) berdasarkan query parameter `type`
    */
    public function product(Request $request)
    {
        // Validasi parameter type
        $type = $request->query('type', 'in'); // default: 'in' jika tidak ada
        $type = in_array($type, ['in', 'out']) ? $type : 'in';

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Ambil transaksi berdasarkan type
        $transactions = Transaction::with('details.product')
            ->where('type', $type)
            ->when($startDate, function ($query, $startDate) {
                return $query->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                return $query->whereDate('created_at', '<=', $endDate);
            })
            ->latest()
            ->paginate(10)
            ->appends($request->except('page'));

        // Hitung total quantity dari detail transaksi
        $grandQuantity = TransactionDetail::whereHas('transaction', function ($query) use ($type, $startDate, $endDate) {
            $query->where('type', $type);
            if ($startDate) {
                $query->whereDate('transactions.created_at', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('transactions.created_at', '<=', $endDate);
            }
        })->sum('quantity');

        return view('admin.transaction.product', compact('transactions', 'grandQuantity', 'type', 'startDate', 'endDate'));
    }

    /**
     * Menghapus transaksi dan mengembalikan stok produk.
     */
    public function destroy(Transaction $transaction)
    {
    DB::transaction(function () use ($transaction) {
        // Cek apakah transaksi memiliki detail
        if ($transaction->details->isNotEmpty()) {
            // Jika ada detail, proses pembaruan stok
            $transactionDetail = $transaction->details->first();
            $product = $transactionDetail->product;
            
            // Lakukan penyesuaian stok berdasarkan tipe transaksi
            if ($transaction->type === 'in') {
                $product->quantity -= $transactionDetail->quantity;
            } elseif ($transaction->type === 'out') {
                $product->quantity += $transactionDetail->quantity;
            }
            
            // Simpan perubahan stok
            $product->save();

            // Hapus detail transaksi
            $transactionDetail->delete();
        }
        
        // Hapus transaksi utama (header)
        $transaction->delete();
    });

    return back()->with('toast_success', 'Transaksi berhasil dihapus dan stok telah disesuaikan.');
}

    /**
     * Ekspor daftar transaksi ke format PDF.
     */
    public function exportPdf(Request $request, $type)
    {
        $type = in_array($type, ['in', 'out']) ? $type : 'in';
    
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
    
        $transactions = Transaction::with('details.product')
            ->where('type', $type)
            ->when($startDate, fn($q) => $q->whereDate('transactions.created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('transactions.created_at', '<=', $endDate))
            ->latest()
            ->get();
    
        $grandQuantity = TransactionDetail::whereHas('transaction', function ($query) use ($type, $startDate, $endDate) {
            $query->where('type', $type);
            if ($startDate) {
                $query->whereDate('transactions.created_at', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('transactions.created_at', '<=', $endDate);
            }
        })->sum('quantity');
    
        $title = $type === 'in' ? 'Laporan Barang Masuk' : 'Laporan Barang Keluar';
        $fileName = strtolower(str_replace(' ', '_', $title)) . '_' . now()->format('Y-m-d_H-i-s') . '.pdf';
    
        $pdf = PDF::loadView('admin.transaction.report.pdf', compact('transactions', 'grandQuantity', 'title'));
        
        return $pdf->download($fileName);
    }
}