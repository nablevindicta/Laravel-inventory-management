<?php

namespace App\Http\Controllers\Admin;

use App\Models\Rent;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TransactionDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionController extends Controller
{
    /**
     * Menampilkan daftar transaksi barang keluar dengan filter tanggal.
     */
    public function product(Request $request)
    {
        $transactions = Transaction::with('details.product')
            ->where('type', 'out') // <-- Pastikan baris ini ada
            ->latest()
            ->paginate(10);

        $grandQuantity = TransactionDetail::whereHas('transaction', function($query) {
            $query->where('type', 'out');
        })->sum('quantity');

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

    // di file App/Http/Controllers/Admin/TransactionController.php

    public function productin()
    {
        $transactions = Transaction::with('details.product')
            ->where('type', 'in') // <-- Pastikan baris ini ada
            ->latest()
            ->paginate(10);

        $grandQuantity = TransactionDetail::whereHas('transaction', function($query) {
            $query->where('type', 'in');
        })->sum('quantity');

        return view('admin.transaction.productin', compact('transactions', 'grandQuantity'));
    }

    // public function vehicle()
    // {
    //     $rents = Rent::with('vehicle', 'user')->when(request()->q, function($search){
    //         $search = $search->whereHas('user', function($query){
    //             $query->where('name', 'like', '%'.request()->q.'%');
    //         })->orWhereHas('vehicle', function($query){
    //             $query->where('name', 'like', '%'.request()->q.'%');
    //         });
    //     })->latest()->paginate(10);

    //     return view('admin.transaction.vehicle', compact('rents'));
    // }
}
