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
        $vehicles = Vehicle::count();
        $suppliers = Supplier::count();
        $products = Product::count();
        $customers = User::count();
        $transactions = TransactionDetail::sum('quantity');
        $transactionThisMonth = TransactionDetail::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('quantity');
        $productsOutStock = Product::where('quantity', '<=', 10)->paginate(5);
        $orders = Order::where('status', 0)->get();

        // Ambil 5 produk terlaris (barang keluar terbanyak)
        $bestProduct = TransactionDetail::with('product')
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
            $total = [1]; // Agar chart tetap muncul
        }

        return view('admin.dashboard', compact(
            'categories',
            'vehicles',
            'suppliers',
            'products',
            'customers',
            'transactions',
            'transactionThisMonth',
            'productsOutStock',
            'orders',
            'label',
            'total'
        ));
    }
}