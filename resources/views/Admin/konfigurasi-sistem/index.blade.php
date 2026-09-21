@extends('layouts.admin')

@section('title', 'Konfigurasi Sistem')

@section('content')

<div class="mx-auto max-w-7xl space-y-6">

    <div>

        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-emerald-700">
            SmartPath Administration
        </p>

        <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">
            Konfigurasi Sistem
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Parameter operasional aplikasi yang dapat dikelola administrator.
        </p>

    </div>


    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white">

        <div class="border-b border-slate-100 px-6 py-4">

            <h2 class="font-semibold text-slate-900">
                Parameter Sistem
            </h2>

            <p class="mt-1 text-xs text-slate-500">
                Perubahan di halaman ini langsung digunakan oleh logika aplikasi.
            </p>

        </div>


        <div class="overflow-x-auto">

            <table class="min-w-full text-sm">

                <thead class="border-b border-slate-200 bg-slate-50">

                    <tr class="text-left text-xs uppercase tracking-wide text-slate-500">

                        <th class="px-6 py-3">
                            Kunci
                        </th>

                        <th class="px-6 py-3">
                            Nilai
                        </th>

                        <th class="px-6 py-3">
                            Tipe
                        </th>

                        <th class="px-6 py-3">
                            Keterangan
                        </th>

                        <th class="px-6 py-3">
                            Status
                        </th>

                        <th class="px-6 py-3 text-right">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse($konfigurasi as $item)

                        <tr class="transition hover:bg-slate-50/70">

                            <td class="px-6 py-4">

                                <code class="rounded bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-800">
                                    {{ $item->kunci }}
                                </code>

                            </td>


                            <td class="max-w-sm px-6 py-4">

                                <span class="break-words font-medium text-slate-900">
                                    {{ \Illuminate\Support\Str::limit($item->nilai, 80) }}
                                </span>

                            </td>


                            <td class="px-6 py-4">

                                <span class="inline-flex rounded-md bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-600">
                                    {{ $item->tipe_nilai }}
                                </span>

                            </td>


                            <td class="max-w-md px-6 py-4 text-xs text-slate-500">
                                {{ $item->keterangan ?: '-' }}
                            </td>


                            <td class="px-6 py-4">

                                @if($item->dapat_diedit_ui)

                                    <span class="text-xs font-semibold text-emerald-700">
                                        Dapat diedit
                                    </span>

                                @else

                                    <span class="text-xs font-semibold text-slate-400">
                                        Terkunci
                                    </span>

                                @endif

                            </td>


                            <td class="px-6 py-4 text-right">

                                @if($item->dapat_diedit_ui)

                                    <a
                                        href="{{ route('admin.konfigurasi-sistem.edit', $item) }}"
                                        class="text-xs font-semibold text-emerald-700 hover:text-emerald-800"
                                    >
                                        Edit
                                    </a>

                                @else

                                    <span class="text-xs text-slate-400">
                                        Read only
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="px-6 py-12 text-center text-slate-400"
                            >
                                Belum ada konfigurasi sistem.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($konfigurasi->hasPages())

            <div class="border-t border-slate-100 px-6 py-4">

                {{ $konfigurasi->links('pagination::tailwind') }}

            </div>

        @endif

    </section>

</div>

@endsection