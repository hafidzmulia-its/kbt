<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Event;
use App\Models\Order;
use App\Models\Template;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'metrics' => [
                'users' => User::count(),
                'events' => Event::count(),
                'templates' => Template::count(),
                'orders' => Order::count(),
            ],
            'users' => User::latest()->take(10)->get(),
            'events' => Event::with('user')->latest()->take(10)->get(),
            'logs' => AuditLog::latest()->take(20)->get(),
        ]);
    }
}
