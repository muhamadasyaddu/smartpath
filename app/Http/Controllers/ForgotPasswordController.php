<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    // Tampilkan form minta link reset password
    public function showLinkRequestForm()
    {
           return view('Auth.forgot-password');
    }

    // Kirim link reset ke email pengguna
    public function sendResetLinkEmail(Request $request)
    {
        $email = Str::lower(trim((string) $request->input('email')));
        $request->merge(['email' => $email]);

        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => 'Email ini tidak terdaftar dalam sistem.'
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', 'Link reset kata sandi telah dikirim ke email kamu!')
            : back()->withErrors(['email' => __($status)]);
    }

    // Tampilkan form ubah password baru
    public function showResetForm(Request $request, $token)
    {
        $email = Str::lower(trim((string) $request->query('email', $request->email)));

        if (blank($email)) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Link reset kata sandi tidak valid atau sudah kedaluwarsa.']);
        }

        return view('Auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    // Update password di database
    public function resetPassword(Request $request)
    {
        $email = Str::lower(trim((string) $request->input('email')));
        $request->merge(['email' => $email]);

        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ], [
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal 8 karakter.'
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'kata_sandi' => Hash::make($password)
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')
                ->with('status', 'Kata sandi berhasil diperbarui! Silakan login dengan kata sandi baru.');
        }

        $message = match ($status) {
            Password::INVALID_TOKEN => 'Link reset kata sandi tidak valid, sudah digunakan, atau kedaluwarsa. Minta link reset yang baru.',
            Password::INVALID_USER => 'Email akun tidak ditemukan. Periksa email atau minta link reset baru.',
            default => __($status),
        };

        return back()
            ->withErrors(['email' => $message])
            ->withInput($request->only('email'));
    }
}
