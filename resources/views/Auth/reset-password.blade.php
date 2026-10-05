@extends('layouts.app') 

@section('content')
<div class="min-h-screen flex items-center justify-center bg-slate-50 px-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
        <h2 class="text-xl font-bold text-slate-900 mb-2">Atur Kata Sandi Baru</h2>
        <p class="text-sm text-slate-500 mb-6">Masukkan kata sandi baru untuk akun kamu.</p>

        @if ($errors->has('email'))
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" role="alert">
                <p>{{ $errors->first('email') }}</p>
                <a href="{{ route('password.request') }}" class="mt-2 inline-block font-semibold text-red-800 underline">
                    Minta link reset baru
                </a>
            </div>
        @endif

        @if (!$errors->has('email'))
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <input type="hidden" id="email" name="email" value="{{ old('email', $email) }}" required>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Kata Sandi Baru</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required autocomplete="new-password"
                        class="w-full rounded-xl border-slate-300 pr-12 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <button type="button" data-password-toggle="password"
                        class="absolute inset-y-0 right-0 flex w-12 items-center justify-center text-slate-400 transition hover:text-emerald-600 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-emerald-500"
                        aria-label="Tampilkan kata sandi baru" aria-pressed="false">
                        <svg data-password-eye viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true">
                            <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/>
                            <circle cx="12" cy="12" r="2.5"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Kata Sandi Baru</label>
                <div class="relative">
                    <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
                        class="w-full rounded-xl border-slate-300 pr-12 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <button type="button" data-password-toggle="password_confirmation"
                        class="absolute inset-y-0 right-0 flex w-12 items-center justify-center text-slate-400 transition hover:text-emerald-600 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-emerald-500"
                        aria-label="Tampilkan konfirmasi kata sandi" aria-pressed="false">
                        <svg data-password-eye viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true">
                            <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/>
                            <circle cx="12" cy="12" r="2.5"/>
                        </svg>
                    </button>
                </div>
            </div>

                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2.5 rounded-xl transition-colors text-sm">
                    Simpan Kata Sandi Baru
                </button>
            </form>
        @endif
    </div>
</div>
<script>
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.passwordToggle);
            const isVisible = input.type === 'text';

            input.type = isVisible ? 'password' : 'text';
            button.setAttribute('aria-pressed', String(!isVisible));
            button.setAttribute(
                'aria-label',
                isVisible ? 'Tampilkan kata sandi' : 'Sembunyikan kata sandi'
            );
            button.querySelector('[data-password-eye]').innerHTML = isVisible
                ? '<path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/>'
                : '<path d="M3 3l18 18"/><path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path d="M9.9 4.2A10.8 10.8 0 0 1 12 4c6.5 0 10 8 10 8a17.8 17.8 0 0 1-3.1 4.3"/><path d="M6.6 6.6C3.8 8.4 2 12 2 12s3.5 8 10 8c1.4 0 2.7-.3 3.9-.9"/>';
        });
    });
</script>
@endsection