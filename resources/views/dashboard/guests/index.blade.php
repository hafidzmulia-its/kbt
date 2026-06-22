@extends('layouts.app', ['title' => 'Guests'])

@php
    $lifecycle = $filters['lifecycle'] ?? 'active';
@endphp

@section('content')
    <section class="dashboard-hero">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-4xl">
                <p class="section-kicker">Guest Operations</p>
                <h1 class="mt-3 text-5xl text-primary md:text-6xl">Kelola tamu, grup, import batch, dan lifecycle undangan dalam satu modul.</h1>
                <p class="mt-5 max-w-3xl text-base leading-8 text-on-surface-variant">
                    Ini adalah lapisan yang paling penting untuk mengejar pengalaman Wedew, tetapi tetap dibuat lebih tenang dan lebih sedikit membingungkan. Tamu, grup, RSVP, personal link, dan archive sekarang tinggal dikelola dari satu tempat.
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('dashboard.events.workspace', $event) }}" class="btn-secondary">Kembali ke workspace</a>
                <a href="{{ route('dashboard.guests.export', array_merge(['event' => $event], $filters)) }}" class="btn-primary">Export CSV</a>
            </div>
        </div>
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-[0.86fr_1.14fr]">
        <div class="space-y-6">
            <div class="surface-panel">
                <p class="section-kicker">Guest Groups</p>
                <h2 class="mt-3 text-4xl text-primary">Buat segment yang benar-benar berguna.</h2>
                <p class="mt-4 section-copy">Grup tamu akan jadi fondasi untuk bulk action, filtering, dan delivery pipeline berikutnya. Jadi kita buat cukup kuat, tapi tidak ribet.</p>

                <form method="POST" action="{{ route('dashboard.guests.groups.store', $event) }}" class="mt-6 grid gap-4">
                    @csrf
                    <input class="field" name="name" placeholder="Nama grup, misal: Keluarga Inti">
                    <textarea class="field min-h-24" name="description" placeholder="Catatan grup atau segmentasi tamu"></textarea>
                    <button class="btn-secondary" type="submit">Tambah grup</button>
                </form>

                <div class="mt-6 space-y-3">
                    @forelse ($groups as $group)
                        <div class="dashboard-table-card">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-lg font-semibold text-primary">{{ $group->name }}</p>
                                    <p class="mt-1 section-copy">{{ $group->description ?: 'Tanpa deskripsi.' }}</p>
                                </div>
                                <span class="dashboard-chip">{{ $group->guests_count }} tamu</span>
                            </div>
                        </div>
                    @empty
                        <div class="module-card">
                            <p class="text-lg font-semibold text-primary">Belum ada grup tamu.</p>
                            <p class="mt-3 section-copy">Mulai dari grup sederhana seperti keluarga, teman kuliah, kantor, atau vendor dekat.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="surface-panel">
                <p class="section-kicker">Manual Entry</p>
                <h2 class="mt-3 text-4xl text-primary">Tambah tamu satu per satu.</h2>
                <form method="POST" action="{{ route('dashboard.guests.store', $event) }}" class="mt-6 grid gap-4">
                    @csrf
                    <input class="field" name="name" placeholder="Nama tamu">
                    <div class="grid gap-4 md:grid-cols-2">
                        <input class="field" name="phone" placeholder="WhatsApp">
                        <select class="field" name="guest_group_id">
                            <option value="">Tanpa grup</option>
                            @foreach ($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <input class="field" name="max_pax" type="number" min="1" value="1" placeholder="Max pax">
                        <select class="field" name="status">
                            <option value="active">active</option>
                            <option value="inactive">inactive</option>
                        </select>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="module-card flex items-center gap-3">
                            <input type="checkbox" name="is_vip" value="1">
                            <span class="text-sm font-semibold text-on-surface">VIP</span>
                        </label>
                        <label class="module-card flex items-center gap-3">
                            <input type="checkbox" name="needs_physical_invitation" value="1">
                            <span class="text-sm font-semibold text-on-surface">Perlu undangan fisik</span>
                        </label>
                    </div>
                    <textarea class="field min-h-24" name="address_note" placeholder="Catatan alamat, hubungan, atau instruksi"></textarea>
                    <button class="btn-primary" type="submit">Simpan tamu</button>
                </form>
            </div>

            <div class="surface-panel">
                <p class="section-kicker">Batch Import</p>
                <h2 class="mt-3 text-4xl text-primary">Preview dulu sebelum commit.</h2>
                <p class="mt-4 section-copy">Header yang didukung: <code>name</code>, <code>phone</code>, <code>group_name</code>, <code>address_note</code>, <code>max_pax</code>, <code>status</code>, <code>is_vip</code>, <code>needs_physical_invitation</code>.</p>

                <form method="POST" action="{{ route('dashboard.guests.import-preview', $event) }}" enctype="multipart/form-data" class="mt-6 space-y-4">
                    @csrf
                    <input class="field" type="file" name="import_file" accept=".csv,.txt,.xlsx">
                    <button class="btn-secondary w-full" type="submit">Preview import CSV/XLSX</button>
                </form>

                @if ($importPreview)
                    <div class="mt-6 rounded-[1.45rem] border border-[#1D5A8D]/14 bg-[#f7fbff] p-5">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.16em] text-primary">Preview aktif</p>
                                <h3 class="mt-2 text-2xl text-primary">{{ $importPreview['file_name'] }}</h3>
                                <p class="mt-2 section-copy">{{ $importPreview['summary']['valid_rows'] }} valid · {{ $importPreview['summary']['invalid_rows'] }} perlu diperbaiki · {{ $importPreview['summary']['total_rows'] }} total</p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <form method="POST" action="{{ route('dashboard.guests.import-commit', $event) }}">
                                    @csrf
                                    <button class="btn-primary" type="submit" @disabled($importPreview['summary']['valid_rows'] === 0)>Commit import</button>
                                </form>
                                <form method="POST" action="{{ route('dashboard.guests.import-preview.clear', $event) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-secondary" type="submit">Buang preview</button>
                                </form>
                            </div>
                        </div>

                        @if (! empty($importPreview['summary']['detected_groups']))
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach ($importPreview['summary']['detected_groups'] as $groupName)
                                    <span class="dashboard-chip">{{ $groupName }}</span>
                                @endforeach
                            </div>
                        @endif

                        <div class="mt-5 overflow-x-auto">
                            <table class="dashboard-data-table">
                                <thead>
                                    <tr>
                                        <th>Row</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Group</th>
                                        <th>Flags</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($importPreview['rows'] as $row)
                                        <tr>
                                            <td>#{{ $row['row_number'] }}</td>
                                            <td>
                                                <p class="font-semibold text-primary">{{ $row['data']['name'] ?: '-' }}</p>
                                                @if ($row['errors'])
                                                    <p class="mt-2 text-xs text-[#ad3d2c]">{{ implode(' ', $row['errors']) }}</p>
                                                @endif
                                            </td>
                                            <td>{{ $row['data']['phone'] ?: '-' }}</td>
                                            <td>{{ $row['data']['group_name'] ?: '-' }}</td>
                                            <td>
                                                <div class="flex flex-wrap gap-2">
                                                    @if ($row['data']['is_vip'])
                                                        <span class="dashboard-chip">VIP</span>
                                                    @endif
                                                    @if ($row['data']['needs_physical_invitation'])
                                                        <span class="dashboard-chip">Fisik</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>{{ $row['data']['status'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="surface-panel">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="section-kicker">Live Directory</p>
                    <h2 class="mt-3 text-4xl text-primary">Filter tamu, jalankan bulk action, lalu rapikan lifecycle.</h2>
                </div>
                <p class="section-copy max-w-xl">Default view menampilkan tamu aktif. Arsip tidak dihapus permanen supaya bisa dipulihkan kapan saja.</p>
            </div>

            <form method="GET" action="{{ route('dashboard.guests.index', $event) }}" class="mt-6 grid gap-4 lg:grid-cols-[1.2fr_0.7fr_0.8fr_0.7fr_0.72fr_0.95fr_auto]">
                <input class="field" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama, phone, atau group">
                <select class="field" name="status">
                    <option value="">Semua status</option>
                    @foreach (['active', 'inactive'] as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
                <select class="field" name="guest_group_id">
                    <option value="">Semua grup</option>
                    @foreach ($groups as $group)
                        <option value="{{ $group->id }}" @selected((string) ($filters['guest_group_id'] ?? '') === (string) $group->id)>{{ $group->name }}</option>
                    @endforeach
                </select>
                <select class="field" name="lifecycle">
                    <option value="active" @selected($lifecycle === 'active')>Aktif</option>
                    <option value="archived" @selected($lifecycle === 'archived')>Arsip</option>
                    <option value="all" @selected($lifecycle === 'all')>Semua</option>
                </select>
                <label class="module-card flex items-center gap-3">
                    <input type="checkbox" name="vip_only" value="1" @checked(($filters['vip_only'] ?? '') === '1')>
                    <span class="text-sm font-semibold text-on-surface">VIP only</span>
                </label>
                <label class="module-card flex items-center gap-3">
                    <input type="checkbox" name="physical_only" value="1" @checked(($filters['physical_only'] ?? '') === '1')>
                    <span class="text-sm font-semibold text-on-surface">Undangan fisik</span>
                </label>
                <button class="btn-primary" type="submit">Filter</button>
            </form>

            <form id="guest-bulk-form" method="POST" action="{{ route('dashboard.guests.bulk', $event) }}" class="mt-6 rounded-[1.45rem] border border-outline-variant/18 bg-[#f7fbff] p-4">
                @csrf
                <div class="grid gap-3 lg:grid-cols-[1fr_0.9fr_auto]">
                    <select class="field" name="action">
                        <option value="assign_group">Assign group</option>
                        <option value="clear_group">Clear group</option>
                        <option value="mark_vip">Mark VIP</option>
                        <option value="unmark_vip">Unmark VIP</option>
                        <option value="require_physical">Require physical invitation</option>
                        <option value="clear_physical">Clear physical invitation</option>
                        <option value="activate">Set active</option>
                        <option value="deactivate">Set inactive</option>
                        <option value="archive">Archive selected</option>
                        <option value="restore">Restore selected</option>
                        <option value="regenerate_tokens">Regenerate tokens</option>
                    </select>
                    <select class="field" name="guest_group_id">
                        <option value="">Pilih grup untuk assign</option>
                        @foreach ($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn-secondary" type="submit">Jalankan bulk action</button>
                </div>
            </form>

            <div class="mt-6 space-y-4">
                @forelse ($guests as $guest)
                    @php
                        $latestRsvp = $guest->rsvps->sortByDesc('submitted_at')->first();
                        $gift = $guest->giftContributions->sortByDesc('updated_at')->first();
                    @endphp
                    <details class="module-card">
                        <summary class="flex list-none flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                            <div class="flex items-start gap-4">
                                <input
                                    type="checkbox"
                                    form="guest-bulk-form"
                                    name="guest_ids[]"
                                    value="{{ $guest->id }}"
                                    class="mt-1 h-5 w-5 cursor-pointer"
                                >
                                <div class="max-w-3xl">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="dashboard-chip">{{ $guest->trashed() ? 'Archived' : ucfirst($guest->status) }}</span>
                                        <span class="text-sm text-on-surface-variant">{{ $guest->resolved_group_name ?: 'Tanpa grup' }}</span>
                                        @if ($guest->is_vip)
                                            <span class="dashboard-chip">VIP</span>
                                        @endif
                                        @if ($guest->needs_physical_invitation)
                                            <span class="dashboard-chip">Undangan fisik</span>
                                        @endif
                                    </div>
                                    <h3 class="mt-4 text-2xl text-primary">{{ $guest->name }}</h3>
                                    <p class="mt-2 section-copy">{{ $guest->phone ?: 'Tanpa phone' }} · max {{ $guest->max_pax }} pax.</p>
                                    @if ($guest->invitation)
                                        <a href="{{ $guest->invitation->invitation_url_cached }}" class="mt-3 inline-block break-all text-sm font-semibold text-primary">{{ $guest->invitation->invitation_url_cached }}</a>
                                    @endif
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-2 text-center text-xs">
                                <div class="metric-card min-w-24">
                                    <p class="uppercase tracking-[0.18em] text-on-surface-variant">RSVP</p>
                                    <p class="mt-2 font-semibold text-primary">{{ $latestRsvp?->status ?? 'pending' }}</p>
                                </div>
                                <div class="metric-card min-w-24">
                                    <p class="uppercase tracking-[0.18em] text-on-surface-variant">Check-in</p>
                                    <p class="mt-2 font-semibold text-primary">{{ $guest->attendanceCheckins->isNotEmpty() ? 'yes' : 'no' }}</p>
                                </div>
                                <div class="metric-card min-w-24">
                                    <p class="uppercase tracking-[0.18em] text-on-surface-variant">Gift</p>
                                    <p class="mt-2 font-semibold text-primary">{{ $gift?->status ?? 'none' }}</p>
                                </div>
                            </div>
                        </summary>

                        <div class="mt-5 grid gap-4 lg:grid-cols-[1fr_auto]">
                            <form method="POST" action="{{ route('dashboard.guests.update', [$event, $guest]) }}" class="grid gap-4 lg:grid-cols-2">
                                @csrf
                                @method('PUT')
                                <input class="field" name="name" value="{{ $guest->name }}" placeholder="Nama tamu">
                                <input class="field" name="phone" value="{{ $guest->phone }}" placeholder="WhatsApp">
                                <select class="field" name="guest_group_id">
                                    <option value="">Tanpa grup</option>
                                    @foreach ($groups as $group)
                                        <option value="{{ $group->id }}" @selected($guest->guest_group_id === $group->id)>{{ $group->name }}</option>
                                    @endforeach
                                </select>
                                <input class="field" name="max_pax" type="number" min="1" max="10" value="{{ $guest->max_pax }}" placeholder="Max pax">
                                <textarea class="field min-h-24 lg:col-span-2" name="address_note" placeholder="Catatan">{{ $guest->address_note }}</textarea>
                                <div class="grid gap-4 lg:col-span-2 md:grid-cols-3">
                                    <select class="field" name="status">
                                        @foreach (['active', 'inactive'] as $status)
                                            <option value="{{ $status }}" @selected($guest->status === $status)>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                    <label class="module-card flex items-center gap-3">
                                        <input type="checkbox" name="is_vip" value="1" @checked($guest->is_vip)>
                                        <span class="text-sm font-semibold text-on-surface">VIP</span>
                                    </label>
                                    <label class="module-card flex items-center gap-3">
                                        <input type="checkbox" name="needs_physical_invitation" value="1" @checked($guest->needs_physical_invitation)>
                                        <span class="text-sm font-semibold text-on-surface">Undangan fisik</span>
                                    </label>
                                </div>
                                <div class="flex flex-wrap gap-2 lg:col-span-2">
                                    <button class="btn-primary" type="submit">Update tamu</button>
                                </div>
                            </form>

                            <div class="flex flex-wrap gap-2 lg:flex-col">
                                @if (! $guest->trashed())
                                    <form method="POST" action="{{ route('dashboard.guests.regenerate-token', [$event, $guest]) }}">
                                        @csrf
                                        <button class="btn-secondary w-full" type="submit">Regenerate token</button>
                                    </form>
                                    <form method="POST" action="{{ route('dashboard.guests.destroy', [$event, $guest]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn-secondary w-full" type="submit">Archive</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('dashboard.guests.restore', [$event, $guest->id]) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn-secondary w-full" type="submit">Restore</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </details>
                @empty
                    <div class="module-card">
                        <p class="text-lg font-semibold text-primary">Belum ada tamu yang cocok dengan filter saat ini.</p>
                        <p class="mt-3 section-copy">Perbarui keyword, grup, atau lifecycle filter, atau mulai tambahkan tamu baru dari panel sebelah kiri.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $guests->links() }}
            </div>
        </div>
    </section>
@endsection
