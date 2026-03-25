<?php

namespace App\Http\Controllers;

use App\Models\Order;

class OrderController extends Controller
{
    /**
     * Display all orders for the authenticated buyer.
     */
    public function index()
    {
        $orders = Order::with(['product', 'maker'])
            ->where('buyer_id', auth()->id())
            ->orderByDesc('created_at')
            ->get();

        return view('pages.orders.index', [
            'orders' => $orders,
        ]);
    }
}
