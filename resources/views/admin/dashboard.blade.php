@extends('layouts.app', ['title' => 'Admin'])

@section('content')
    <div class="grid gap-6 lg:grid-cols-4">
        @foreach ($metrics as $label => $value)
            <div class="panel">
                <p class="text-sm uppercase tracking-[0.3em] text-slate-500">{{ $label }}</p>
                <p class="mt-4 text-4xl font-semibold text-slate-900">{{ $value }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <a href="{{ route('admin.templates.index') }}" class="panel cursor-pointer transition-colors hover:border-slate-400">Kelola template</a>
        <a href="{{ route('admin.music.index') }}" class="panel cursor-pointer transition-colors hover:border-slate-400">Kelola backsound</a>
        <a href="{{ route('admin.orders.index') }}" class="panel cursor-pointer transition-colors hover:border-slate-400">Kelola order</a>
    </div>

    <div class="mt-6 panel">
        <h2 class="text-3xl font-semibold text-slate-900">Audit logs</h2>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-left text-slate-500">
                    <tr>
                        <th class="py-2">Waktu</th>
                        <th class="py-2">Actor</th>
                        <th class="py-2">Action</th>
                        <th class="py-2">Subject</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr class="border-t border-slate-200">
                            <td class="py-3">{{ $log->created_at }}</td>
                            <td class="py-3">{{ $log->actor_type }}</td>
                            <td class="py-3">{{ $log->action }}</td>
                            <td class="py-3">{{ $log->subject_type }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <div class="panel">
            <h2 class="text-3xl font-semibold text-slate-900">User terbaru</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-slate-500">
                        <tr>
                            <th class="py-2">Nama</th>
                            <th class="py-2">Email</th>
                            <th class="py-2">Role</th>
                            <th class="py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr class="border-t border-slate-200">
                                <td class="py-3">{{ $user->name }}</td>
                                <td class="py-3">{{ $user->email }}</td>
                                <td class="py-3">{{ $user->role }}</td>
                                <td class="py-3">{{ $user->status ?? 'active' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel">
            <h2 class="text-3xl font-semibold text-slate-900">Event terbaru</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-slate-500">
                        <tr>
                            <th class="py-2">Event</th>
                            <th class="py-2">Pemilik</th>
                            <th class="py-2">Status</th>
                            <th class="py-2">Publikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($events as $event)
                            <tr class="border-t border-slate-200">
                                <td class="py-3">{{ $event->couple_name_display }}</td>
                                <td class="py-3">{{ $event->user?->name }}</td>
                                <td class="py-3">{{ $event->status }}</td>
                                <td class="py-3">{{ optional($event->published_at)->format('d M Y H:i') ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
