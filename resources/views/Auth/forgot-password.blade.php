@extends('layouts.app') 

@section('content')
<div class="min-h-screen flex items-center justify-center bg-slate-50 px-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
        <h2 class="text-xl font-bold text-slate-900 mb-2">Lupa Kata Sandi?</h2>

        @if (session('status'))
            <!-- Tampilan kalau email SUDAH terkirim -->
            <div class="text-center py-2">
                <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 mb-1">Cek Email Kamu!</h3>
                <p class="text-sm text-slate-500 mb-6">{{ session('status') }}</p>
                
                <a href="https://mail.google.com" target="_blank" class="inline-flex items-center justify-center w-full px-4 py-2.5 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-colors shadow-sm">
                    Buka Gmail
                </a>
            </div>
        @else
            <!-- Form kalau BELUM kirim email -->
            <p class="text-sm text-slate-500 mb-6">Masukkan email kamu dan kami akan mengirimkan link untuk mereset kata sandi.</p>

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="mb-5">
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Alamat Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="contoh@gmail.com"
                        class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2.5 rounded-xl transition-colors text-sm">
                    Kirim Link Reset
                </button>
            </form>
        @endif
    </div>
</div>
@endsection