<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Schema;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Real counts from database
        $totalSales = Schema::hasTable('orders') ? Order::where('order_status', '<>', 'cancelled')->sum('grand_total') : 0;
        $totalOrders = Schema::hasTable('orders') ? Order::count() : 0;
        $totalUsers = User::count();
        $totalProducts = Product::count();

        // Recent Users
        $recentUsers = User::latest()->limit(5)->get();

        // Latest 10 Orders
        $latestOrders = Order::latest()->limit(10)->get();

        // Shiprocket Wallet (Step 12)
        $shiprocket = new \App\Services\ShiprocketService();
        $srWalletRes = $shiprocket->getWalletBalance();
        $srWallet = $srWalletRes['status'] ? $srWalletRes['data']['data']['wallet_balance'] : 'N/A';

        return view('admin.dashboard', compact(
            'totalSales',
            'totalOrders',
            'totalUsers',
            'totalProducts',
            'recentUsers',
            'latestOrders',
            'srWallet'
        ));
    }
}
