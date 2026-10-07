<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use App\Models\Audit;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (auth()->check()) {
            $user = auth()->user();

            return redirect()->route(
                $user->getDashboardRouteName()
            );
        }

        /*  
         * Nama view disesuaikan dengan struktur folder
         * project yang sekarang.
         */
        return view('Auth.login');
    }

    /**
     * Login menggunakan email dan password.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => [
                'required',
                'string',
                'email',
                'max:150',
            ],
            'kata_sandi' => [
                'required',
                'string',
            ],
        ], [
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'kata_sandi.required' => 'Kata sandi harus diisi.',
        ]);

        $credentials['email'] = Str::lower(trim($credentials['email']));
        $request->merge(['email' => $credentials['email']]);

        $user = User::whereRaw('LOWER(email) = ?', [$credentials['email']])->first();

        if (
            !$user ||
            !Hash::check(
                $credentials['kata_sandi'],
                $user->kata_sandi
            )
        ) {
            return back()
                ->withErrors([
                    'email' => 'Email atau kata sandi salah.',
                ])
                ->onlyInput('email');
        }

        if (!$user->aktif) {
            return back()
                ->withErrors([
                    'email' => 'Akun Anda telah dinonaktifkan. Hubungi administrator.',
                ])
                ->onlyInput('email');
        }

        /*
         * Login setelah kredensial benar.
         */
        Auth::login(
            $user,
            $request->boolean('remember')
        );

        /*
         * Regenerasi session untuk mencegah
         * session fixation.
         */
        $request->session()->regenerate();

        /*
         * Catat waktu login.
         */
        $user->update([
            'terakhir_masuk' => now(),
        ]);

        /*
         * Audit login.
         */
        Audit::log(
            $user->id,
            'login',
            'users',
            $user->id,
            null,
            null,
            'Pengguna berhasil masuk'
        );

        /*
         * Dashboard selalu ditentukan berdasarkan role.
         *
         * Kita tidak menggunakan intended() untuk redirect
         * role agar warga tidak dapat diarahkan ke area admin
         * melalui URL tujuan sebelumnya.
         */
        return redirect()->route(
            $user->getDashboardRouteName()
        );
    }

    /**
     * Menampilkan form registrasi.
     */
    public function showRegisterForm(): View|RedirectResponse
    {
        if (auth()->check()) {
            $user = auth()->user();

            return redirect()->route(
                $user->getDashboardRouteName()
            );
        }

        return view('Auth.register');
    }

    /**
     * Registrasi warga.
     */
    public function register(
        RegisterUserRequest $request
    ): RedirectResponse {
        $validated = $request->validated();
        $validated['email'] = Str::lower(trim($validated['email']));
        $request->merge(['email' => $validated['email']]);

        $user = DB::transaction(
            function () use ($validated) {
                $user = User::create([
                    'nama_lengkap' => $validated['nama_lengkap'],
                    'email' => $validated['email'],
                    'kata_sandi' => Hash::make(
                        $validated['kata_sandi']
                    ),
                    'nomor_hp' => $validated['nomor_hp'] ?? null,
                    'peran' => 'warga',
                    'email_terverifikasi' => false,
                    'aktif' => true,
                ]);

                Audit::log(
                    $user->id,
                    'registrasi',
                    'users',
                    $user->id,
                    null,
                    [
                        'nama_lengkap' => $user->nama_lengkap,
                        'email' => $user->email,
                        'peran' => $user->peran,
                    ],
                    'Akun warga baru berhasil dibuat'
                );

                return $user;
            }
        );

        Auth::login($user);

        
        $request->session()->regenerate();

        return redirect()
        ->route('warga.dashboard')
        ->with(
            'sukses',
            'Akun berhasil dibuat. Selamat datang di SmartPath.'
    );
    }

    /**
     * Redirect ke Google OAuth.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Callback Google OAuth.
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            /*
             * Jangan menggunakan stateless() di sini.
             *
             * Web session + state protection tetap digunakan
             * untuk perlindungan OAuth.
             */
            $googleUser = Socialite::driver('google')->user();

            $googleEmail = $googleUser->getEmail();

            if (!$googleEmail) {
                return redirect()
                    ->route('login')
                    ->withErrors([
                        'email' => 'Google tidak memberikan alamat email yang valid.',
                    ]);
            }

            $user = DB::transaction(
                function () use (
                    $googleUser,
                    $googleEmail
                ) {
                    $existingUser = User::where(
                        'email',
                        $googleEmail
                    )->first();

                    /*
                     * Akun sudah ada.
                     */
                    if ($existingUser) {
                        if (!$existingUser->aktif) {
                            return null;
                        }

                        $existingUser->update([
                            'google_id' => $googleUser->getId(),
                            'email_terverifikasi' => true,
                            'terakhir_masuk' => now(),
                        ]);

                        return $existingUser;
                    }

                    /*
                     * Akun baru dari Google.
                     */
                    $newUser = User::create([
                        'nama_lengkap' =>
                            $googleUser->getName()
                            ?: $googleEmail,

                        'email' => $googleEmail,

                        'google_id' =>
                            $googleUser->getId(),

                        /*
                         * Password acak.
                         * User tetap dapat menggunakan
                         * Google OAuth untuk login.
                         */
                        'kata_sandi' => Hash::make(
                            Str::random(32)
                        ),

                        'peran' => 'warga',

                        'email_terverifikasi' => true,

                        'aktif' => true,

                        'terakhir_masuk' => now(),
                    ]);

                    Audit::log(
                        $newUser->id,
                        'registrasi_google',
                        'users',
                        $newUser->id,
                        null,
                        [
                            'nama_lengkap' =>
                                $newUser->nama_lengkap,

                            'email' =>
                                $newUser->email,

                            'peran' =>
                                $newUser->peran,
                        ],
                        'Akun baru berhasil dibuat melalui Google OAuth'
                    );

                    return $newUser;
                }
            );

            if (!$user) {
                return redirect()
                    ->route('login')
                    ->withErrors([
                        'email' =>
                            'Akun Anda telah dinonaktifkan. Hubungi administrator.',
                    ]);
            }

            /*
             * Login Google.
             */
            Auth::login($user, true);

            /*
             * Regenerasi session.
             */
            request()
                ->session()
                ->regenerate();

            /*
             * Update login terakhir.
             */
            $user->update([
                'terakhir_masuk' => now(),
            ]);

            /*
             * Audit Google login.
             */
            Audit::log(
                $user->id,
                'login_google',
                'users',
                $user->id,
                null,
                null,
                'Pengguna berhasil masuk menggunakan Google OAuth'
            );

            /*
             * Redirect berdasarkan role.
             */
            return redirect()
                ->route(
                    $user->getDashboardRouteName()
                )
                ->with(
                    'sukses',
                    'Berhasil masuk menggunakan akun Google!'
                );

        } catch (\Throwable $e) {
            /*
             * Jangan tampilkan detail exception kepada user.
             * Detail tetap dapat dicatat melalui logging Laravel
             * jika diperlukan.
             */
            report($e);

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' =>
                        'Gagal masuk menggunakan Google. Silakan coba lagi.',
                ]);
        }
    }

    /**
     * Logout pengguna.
     */
    public function logout(Request $request): RedirectResponse
    {
        if (auth()->check()) {
            Audit::log(
                auth()->id(),
                'logout',
                'users',
                auth()->id(),
                null,
                null,
                'Pengguna keluar'
            );
        }

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('beranda');
    }
}