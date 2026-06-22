<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        return view('admin.orders.index', [
            'orders' => Order::query()->with(['user', 'event'])->latest()->get(),
        ]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $order->update($request->validate([
            'status' => ['required', 'string', 'max:50'],
        ]));

        return back()->with('status', 'Status order diperbarui.');
    }
}
