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
        $inboundGoodsCount = TransactionDetail::whereHas('transaction', function ($query) {
            $query->where('type', 'in');
        })->sum('quantity');

        $outboundGoodsCount = TransactionDetail::whereHas('transaction', function ($query) {
            $query->where('type', 'out');
        })->sum('quantity');

        // 3. Hitung jumlah barang keluar bulan ini
        $outboundThisMonthCount = TransactionDetail::whereHas('transaction', function ($query) {
            $query->where('type', 'out');
        })
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->sum('quantity');
        $productsOutStock = Product::where('quantity', '<=', 10)->paginate(5);
        $orders = Order::where('status', 0)->get();

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
            $total = [1]; // Agar chart tetap muncul
        }

        return view('admin.dashboard', compact(
            'categories',
            'suppliers',
            'products',
            'customers',
            'inboundGoodsCount',
            'outboundGoodsCount',
            'outboundThisMonthCount',
            'productsOutStock',
            'orders',
            'label',
            'total'
        ));
    }
}