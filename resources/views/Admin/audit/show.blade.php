@extends('layouts.admin')

@section('title', 'Detail Aktivitas')

@section('content')

<div class="smartpath-admin-page max-w-[1100px] mx-auto space-y-5">

    <div class="flex items-center gap-3">

        <a
            href="{{ route('admin.audit.index') }}"
            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-300 bg-white text-slate-600 hover:bg-slate-50"
            aria-label="Kembali ke riwayat aktivitas"
        >
            ←
        </a>

        <div>

            <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-emerald-700">
                Audit Log
            </p>

            <h1 class="mt-1 text-xl font-semibold text-slate-950">
                Detail Aktivitas
            </h1>

        </div>

    </div>


    <section class="rounded-xl border border-slate-200 bg-white shadow-sm">

        <div class="grid grid-cols-1 gap-5 border-b border-slate-200 p-5 md:grid-cols-2">

            <div>
                <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                    Aktivitas
                </p>

                <p class="mt-1 text-sm font-semibold text-slate-900">
                    {{ ucwords(str_replace('_', ' ', $audit->aksi)) }}
                </p>
            </div>


            <div>
                <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                    Waktu
                </p>

                <p class="mt-1 text-sm text-slate-700">
                    {{ $audit->created_at?->format('d M Y H:i:s') }}
                </p>
            </div>


            <div>
                <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                    Pengguna
                </p>

                <p class="mt-1 text-sm text-slate-700">
                    {{ $audit->pengguna?->nama_lengkap ?? 'Sistem' }}
                </p>
            </div>


            <div>
                <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                    IP Address
                </p>

                <p class="mt-1 text-sm text-slate-700">
                    {{ $audit->ip_address ?? '—' }}
                </p>
            </div>


            <div>
                <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                    Modul
                </p>

                <p class="mt-1 text-sm text-slate-700">
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
                </p>
            </div>


            <div>
                <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                    ID Data
                </p>

                <p class="mt-1 text-sm text-slate-700">
                    {{ $audit->id_terkait ?? '—' }}
                </p>
            </div>

        </div>


        <div class="space-y-5 p-5">

            <div>

                <h2 class="text-sm font-semibold text-slate-900">
                    Keterangan
                </h2>

                <p class="mt-2 rounded-lg bg-slate-50 p-4 text-xs leading-5 text-slate-700">
                    {{ $audit->keterangan ?: 'Tidak ada keterangan.' }}
                </p>

            </div>


            <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">

                <div>

                    <h2 class="text-sm font-semibold text-slate-900">
                        Data Sebelum
                    </h2>

                    <pre class="mt-2 max-h-[400px] overflow-auto rounded-lg bg-slate-950 p-4 text-[10px] leading-5 text-slate-100">{{ json_encode($audit->data_lama, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>

                </div>


                <div>

                    <h2 class="text-sm font-semibold text-slate-900">
                        Data Sesudah
                    </h2>

                    <pre class="mt-2 max-h-[400px] overflow-auto rounded-lg bg-slate-950 p-4 text-[10px] leading-5 text-slate-100">{{ json_encode($audit->data_baru, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>

                </div>

            </div>


            <div>

                <h2 class="text-sm font-semibold text-slate-900">
                    User Agent
                </h2>

                <p class="mt-2 break-all rounded-lg bg-slate-50 p-4 text-[10px] leading-5 text-slate-600">
                    {{ $audit->user_agent ?: '—' }}
                </p>

            </div>

        </div>

    </section>

</div>

@endsection