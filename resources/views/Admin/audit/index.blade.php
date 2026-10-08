@extends('layouts.admin')

@section('title', 'Riwayat Aktivitas')

@section('content')

<div class="smartpath-admin-page max-w-[1400px] mx-auto space-y-5">

    <div>
        <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-emerald-700">
            Administrasi
        </p>

        <h1 class="mt-1 text-xl font-semibold text-slate-950">
            Riwayat Aktivitas
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Rekam aktivitas administrator dan perubahan data SmartPath.
        </p>
    </div>


    <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">

        <form
            method="GET"
            class="grid grid-cols-1 gap-3 md:grid-cols-4"
        >

            <div>
                <label
                    for="aksi"
                    class="mb-1 block text-[10px] font-semibold text-slate-600"
                >
                    Aktivitas
                </label>

                <select
                    id="aksi"
                    name="aksi"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700"
                >
                    <option value="">
                        Semua aktivitas
                    </option>

                    @foreach($aksiList as $aksi)
                        <option
                            value="{{ $aksi }}"
                            @selected(request('aksi') === $aksi)
                        >
                            {{ ucwords(str_replace('_', ' ', $aksi)) }}
                        </option>
                    @endforeach

                </select>
            </div>


            <div>
                <label
                    for="tabel"
                    class="mb-1 block text-[10px] font-semibold text-slate-600"
                >
                    Modul
                </label>

                <select
                    id="tabel"
                    name="tabel"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700"
                >
                    <option value="">
                        Semua modul
                    </option>

                    @foreach($tabelList as $tabel)
                        <option
                            value="{{ $tabel }}"
                            @selected(request('tabel') === $tabel)
                        >
                            {{ ucwords(str_replace('_', ' ', $tabel)) }}
                        </option>
                    @endforeach

                </select>
            </div>


            <div>
                <label
                    for="dari"
                    class="mb-1 block text-[10px] font-semibold text-slate-600"
                >
                    Dari
                </label>

                <input
                    id="dari"
                    name="dari"
                    type="date"
                    value="{{ request('dari') }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs text-slate-700"
                >
            </div>


            <div>
                <label
                    for="sampai"
                    class="mb-1 block text-[10px] font-semibold text-slate-600"
                >
                    Sampai
                </label>

                <input
                    id="sampai"
                    name="sampai"
                    type="date"
                    value="{{ request('sampai') }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs text-slate-700"
                >
            </div>


            <div class="md:col-span-4 flex justify-end gap-2">

                <a
                    href="{{ route('admin.audit.index') }}"
                    class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50"
                >
                    Reset
                </a>

                <button
                    type="submit"
                    class="rounded-lg bg-emerald-600 px-4 py-2 text-xs font-semibold text-white hover:bg-emerald-700"
                >
                    Terapkan Filter
                </button>

            </div>

        </form>

    </section>


    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 px-4 py-3">

            <h2 class="text-sm font-semibold text-slate-900">
                Aktivitas Sistem
            </h2>

        </div>


        <div class="overflow-x-auto">

            <table class="min-w-full">

                <thead class="bg-slate-50">

                    <tr class="border-b border-slate-200">

                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wide text-slate-500">
                            Waktu
                        </th>

                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wide text-slate-500">
                            Pengguna
                        </th>

                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wide text-slate-500">
                            Aktivitas
                        </th>

                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wide text-slate-500">
                            Modul
                        </th>

                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wide text-slate-500">
                            Keterangan
                        </th>

                        <th class="px-4 py-3 text-right text-[10px] font-semibold uppercase tracking-wide text-slate-500">
                            Detail
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse($auditLog as $audit)

                        <tr class="hover:bg-slate-50">

                            <td class="whitespace-nowrap px-4 py-3 text-[10px] text-slate-500">
                                {{ $audit->created_at?->format('d M Y H:i') }}
                            </td>

                            <td class="px-4 py-3">

                                <div class="text-xs font-semibold text-slate-800">
                                    {{ $audit->pengguna?->nama_lengkap ?? 'Sistem' }}
                                </div>

                                @if($audit->ip_address)
                                    <div class="mt-0.5 text-[9px] text-slate-400">
                                        {{ $audit->ip_address }}
                                    </div>
                                @endif

                            </td>

                            <td class="px-4 py-3">

                                <span class="inline-flex rounded-md border px-2 py-1 text-[9px] font-semibold {{ strtolower($audit->aksi) === 'logout' ? 'border-red-200 bg-red-50 text-red-700' : 'border-emerald-200 bg-emerald-50 text-emerald-700' }}">
                                    {{ ucwords(str_replace('_', ' ', $audit->aksi)) }}
                                </span>

                            </td>

                            <td class="px-4 py-3 text-xs text-slate-600">
                                {{
                                    $audit->tabel_terkait
                                        ? ucwords(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $audit->tabel_terkait
                                            )
                                        )
                                        : '—'
                                }}
                            </td>

                            <td class="max-w-[380px] px-4 py-3 text-xs text-slate-600">
                                <span class="line-clamp-2">
                                    {{ $audit->keterangan ?: '—' }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-right">

                                <a
                                    href="{{ route('admin.audit.show', $audit) }}"
                                    class="text-[10px] font-semibold text-emerald-700 hover:text-emerald-800"
                                >
                                    Lihat
                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="px-6 py-14 text-center text-xs text-slate-400"
                            >
                                Belum ada aktivitas yang tercatat.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </section>


    {{ $auditLog->withQueryString()->links() }}

</div>

@endsection