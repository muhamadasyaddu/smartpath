@extends('layouts.admin')

@section('title', 'Notifikasi Administrator')

@section('content')

<div class="smartpath-admin-page max-w-[1200px] mx-auto space-y-5">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">

        <div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-emerald-700">
                Sistem
            </p>

            <h1 class="mt-1 text-xl font-semibold tracking-tight text-slate-950">
                Notifikasi
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Pemberitahuan penting yang berkaitan dengan operasional SmartPath.
            </p>
        </div>

        @if($belumDibaca > 0)

            <form
                method="POST"
                action="{{ route('admin.notifikasi.read-all') }}"
            >
                @csrf

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700"
                >
                    Tandai Semua Dibaca
                </button>
            </form>

        @endif

    </div>


    @if(session('sukses'))

        <div
            role="alert"
            class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"
        >
            {{ session('sukses') }}
        </div>

    @endif


    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

        <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">

            <div>
                <h2 class="text-sm font-semibold text-slate-900">
                    Pemberitahuan Sistem
                </h2>

                <p class="mt-0.5 text-[11px] text-slate-500">
                    {{ $belumDibaca }} notifikasi belum dibaca
                </p>
            </div>

            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-semibold text-emerald-700">
                {{ $notifikasi->total() }} Total
            </span>

        </div>


        <div class="divide-y divide-slate-100">

            @forelse($notifikasi as $item)

                @php
                    $belum = !$item->sudah_dibaca;
                @endphp

                <div
                    class="
                        flex gap-3 px-4 py-4
                        transition
                        {{ $belum
                            ? 'bg-emerald-50/40'
                            : 'bg-white hover:bg-slate-50'
                        }}
                    "
                >

                    <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                        {{ $belum
                            ? 'bg-emerald-100 text-emerald-700'
                            : 'bg-slate-100 text-slate-500'
                        }}"
                    >

                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"
                            />
                        </svg>

                    </div>


                    <div class="min-w-0 flex-1">

                        <div class="flex flex-wrap items-center gap-2">

                            <h3 class="text-sm font-semibold text-slate-900">
                                {{ $item->judul }}
                            </h3>

                            @if($belum)
                                <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[9px] font-semibold text-emerald-700">
                                    Baru
                                </span>
                            @endif

                        </div>


                        <p class="mt-1 text-xs leading-5 text-slate-600">
                            {{ $item->pesan }}
                        </p>


                        @if($item->laporan)

                            <p class="mt-1.5 text-[10px] text-slate-400">
                                Laporan:
                                <span class="font-semibold text-slate-600">
                                    {{ $item->laporan->kode_laporan }}
                                </span>
                            </p>

                        @endif


                        <p class="mt-1 text-[10px] text-slate-400">
                            {{ $item->created_at?->format('d M Y H:i') }}
                            ·
                            {{ $item->created_at?->diffForHumans() }}
                        </p>

                    </div>


                    <div class="shrink-0">

                        <form
                            method="POST"
                            action="{{ route('admin.notifikasi.read', $item) }}"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-[10px] font-semibold text-slate-600 transition hover:border-emerald-300 hover:text-emerald-700"
                            >
                                {{ $item->tautan ? 'Buka' : 'Tandai' }}
                            </button>

                        </form>

                    </div>

                </div>

            @empty

                <div class="px-6 py-16 text-center">

                    <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-slate-100 text-slate-400">

                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"
                            />
                        </svg>

                    </div>

                    <h3 class="mt-3 text-sm font-semibold text-slate-900">
                        Belum Ada Notifikasi
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Sistem belum memiliki pemberitahuan untuk administrator.
                    </p>

                </div>

            @endforelse

        </div>

    </section>


    @if($notifikasi->hasPages())

        <div>
            {{ $notifikasi->links() }}
        </div>

    @endif

</div>

@endsection