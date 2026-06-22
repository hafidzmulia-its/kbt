@extends('layouts.app', ['title' => 'Orders'])

@section('content')
    <div class="panel">
        <h1 class="text-3xl font-semibold text-slate-900">Orders dan paket</h1>
        <div class="mt-6 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-left text-slate-500">
                    <tr>
                        <th class="py-3">User</th>
                        <th class="py-3">Package</th>
                        <th class="py-3">Total</th>
                        <th class="py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr class="border-t border-slate-200 align-top">
                            <td class="py-3">{{ $order->user->name }}</td>
                            <td class="py-3">{{ $order->package_name }}</td>
                            <td class="py-3">Rp{{ number_format($order->total_price, 0, ',', '.') }}</td>
                            <td class="py-3">
                                <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="flex gap-2">
                                    @csrf
                                    @method('PUT')
                                    <select class="field" name="status">
                                        @foreach (['draft', 'unpaid', 'paid', 'cancelled'] as $status)
                                            <option value="{{ $status }}" @selected($order->status === $status)>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn-secondary" type="submit">Simpan</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
