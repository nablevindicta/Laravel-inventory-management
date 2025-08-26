<?php

namespace App\Http\Controllers\Admin;

use App\Models\Rent;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TransactionDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Menampilkan daftar transaksi barang keluar dengan filter tanggal.
     */
    public function product(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $transactions = Transaction::with('details.product')
            ->where('type', 'out')
            ->when($startDate, function ($query, $startDate) {
                return $query->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                return $query->whereDate('created_at', '<=', $endDate);
            })
            ->latest()
            ->paginate(10)
            ->appends($request->except('page'));
            
        $grandQuantity = TransactionDetail::whereHas('transaction', function($query) use ($startDate, $endDate) {
            $query->where('type', 'out')
                ->when($startDate, function ($query, $startDate) {
                    return $query->whereDate('created_at', '>=', $startDate);
                })
                ->when($endDate, function ($query, $endDate) {
                    return $query->whereDate('created_at', '<=', $endDate);
                });
        })->sum('quantity');

        return view('admin.transaction.product', compact('transactions', 'grandQuantity', 'startDate', 'endDate'));
    }

    /**
     * Menampilkan daftar transaksi barang masuk dengan filter tanggal.
     */
    public function productin(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $transactions = Transaction::with('details.product')
            ->where('type', 'in')
            ->when($startDate, function ($query, $startDate) {
                return $query->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                return $query->whereDate('created_at', '<=', $endDate);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $grandQuantity = TransactionDetail::whereHas('transaction', function($query) use ($startDate, $endDate) {
            $query->where('type', 'in')
                ->when($startDate, function ($query, $startDate) {
                    return $query->whereDate('created_at', '>=', $startDate);
                })
                ->when($endDate, function ($query, $endDate) {
                    return $query->whereDate('created_at', '<=', $endDate);
                });
        })->sum('quantity');

        return view('admin.transaction.productin', compact('transactions', 'grandQuantity', 'startDate', 'endDate'));
    }

    /**
     * Menghapus transaksi dan mengembalikan stok produk.
     */
    public function destroy(Transaction $transaction)
    {
        DB::transaction(function () use ($transaction) {
            $transactionDetail = $transaction->details->first();
            $product = $transactionDetail->product;
            
            if ($transaction->type === 'in') {
                $product->quantity -= $transactionDetail->quantity;
            } elseif ($transaction->type === 'out') {
                $product->quantity += $transactionDetail->quantity;
            }
            
            $product->save();
            $transactionDetail->delete();
            $transaction->delete();
        });

        return back()->with('toast_success', 'Transaksi berhasil dihapus dan stok telah disesuaikan.');
    }
}