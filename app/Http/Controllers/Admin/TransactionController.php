<?php

namespace App\Http\Controllers\Admin;

use App\Models\Rent;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TransactionDetail;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
    public function product()
    {
        $transactions = Transaction::with('details.product')
            ->where('type', 'out') // <-- Pastikan baris ini ada
            ->latest()
            ->paginate(10);

        $grandQuantity = TransactionDetail::whereHas('transaction', function($query) {
            $query->where('type', 'out');
        })->sum('quantity');

        return view('admin.transaction.product', compact('transactions', 'grandQuantity'));
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
