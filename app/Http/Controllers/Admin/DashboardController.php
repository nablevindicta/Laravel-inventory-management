<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Vehicle;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $categories = Category::count();
        // $vehicles = Vehicle::count();
        $suppliers = Supplier::count();
        $products = Product::count();
        $customers = User::count();

        $productInQuantity = TransactionDetail::whereHas('transaction', function ($query) {
            $query->where('type', 'in');
        })->sum('quantity');

        $productOutQuantity = TransactionDetail::whereHas('transaction', function ($query) {
            $query->where('type', 'out');
        })->sum('quantity');

        $productInThisMonth = TransactionDetail::whereHas('transaction', function ($query) {
            $query->where('type', 'in');
        })
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('quantity');
        
        $productOutThisMonth = TransactionDetail::whereHas('transaction', function ($query) {
            $query->where('type', 'out');
        })
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('quantity');
        
        // Ambil parameter per_page dari request, default 10
        $perPage = $request->query('per_page', 10);

        $perPage = in_array($perPage, [10, 25, 50]) ? $perPage : 10;

        $productsOutStock = Product::where('quantity', '<=', 10)
            ->with('category')
            ->paginate($perPage);

        // Ambil 5 produk terlaris (barang keluar terbanyak)
        $bestProduct = TransactionDetail::with('product')
            ->whereHas('transaction', function($query) {
                // Tambahkan filter ini untuk hanya mengambil transaksi 'out'
                $query->where('type', 'out');
            })
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->selectRaw('product_id, SUM(quantity) as total')
            ->groupBy('product_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        if ($bestProduct->isNotEmpty()) {
            $label = $bestProduct->map(fn($item) => $item->product?->name ?? 'Tidak Diketahui')->toArray();
            $total = $bestProduct->map(fn($item) => (int)$item->total)->toArray();
        } else {
            $label = ['Tidak Ada Data'];
            $total = [1];
        }

        return view('admin.dashboard', compact(
            'categories',
            'suppliers',
            'products',
            'customers',
            'productInQuantity',
            'productOutQuantity',
            'productInThisMonth',
            'productOutThisMonth',
            'productsOutStock',
            'label',
            'total'
        ));
    }
}