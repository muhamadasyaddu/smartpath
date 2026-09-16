@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
    {{-- Header & Tombol Tandai Semua Dibaca --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-gray-200 mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                Notifikasi
                @if($belumDibaca > 0)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800" aria-label="{{ $belumDibaca }} notifikasi belum dibaca">
                        {{ $belumDibaca }} Baru
                    </span>
                @endif
            </h1>
            <p class="text-sm text-gray-500 mt-1">Daftar pemberitahuan dan pembaruan aktivitas Anda.</p>
        </div>

        @if($belumDibaca > 0)
            <form action="{{ route('notifikasi.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
                    Tandai Semua Dibaca
                </button>
            </form>
        @endif
    </div>

    {{-- Flash Message Sukses --}}
    @if(session('sukses'))
        <div role="alert" class="mb-6 p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-sm text-emerald-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span>{{ session('sukses') }}</span>
        </div>
    @endif

    {{-- Daftar Notifikasi --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 divide-y divide-gray-100 overflow-hidden">
        @forelse($notifikasi as $item)
            @php
                $isBelumDibaca = is_null($item->dibaca_pada) || !$item->is_read;
            @endphp
            <div class="p-4 sm:p-5 flex items-start justify-between gap-4 transition-colors {{ $isBelumDibaca ? 'bg-emerald-50/40 hover:bg-emerald-50/70' : 'hover:bg-gray-50' }}">
                <div class="flex items-start gap-3 flex-1">
                    {{-- Indikator Belum Dibaca --}}
                    @if($isBelumDibaca)
                        <span class="w-2.5 h-2.5 mt-1.5 rounded-full bg-emerald-600 flex-shrink-0" title="Belum dibaca"></span>
                    @else
                        <span class="w-2.5 h-2.5 mt-1.5 rounded-full bg-transparent flex-shrink-0"></span>
                    @endif

                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 leading-snug">
                            {{ $item->pesan ?? $item->judul }}
                        </p>
                        
                        @if($item->laporan)
                            <p class="text-xs text-gray-500 mt-1">
                                Laporan: <span class="font-semibold">{{ $item->laporan->judul ?? '#' . $item->laporan->id }}</span>
                            </p>
                        @endif

                        <span class="text-xs text-gray-400 mt-2 block">
                            {{ $item->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>

                {{-- Action Button --}}
                <div class="flex items-center gap-2 flex-shrink-0">
                    <form action="{{ route('notifikasi.read', $item->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded-md border border-gray-300 text-gray-700 hover:bg-white hover:text-emerald-600 transition-colors">
                            {{ $item->tautan ? 'Buka' : 'Tandai Dibaca' }}
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <h3 class="mt-2 text-sm font-semibold text-gray-900">Tidak Ada Notifikasi</h3>
                <p class="mt-1 text-xs text-gray-500">Saat ini belum ada pemberitahuan baru untuk Anda.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination Links --}}
    @if($notifikasi->hasPages())
        <div class="mt-6">
            {{ $notifikasi->links() }}
        </div>
    @endif
</div>
@endsection