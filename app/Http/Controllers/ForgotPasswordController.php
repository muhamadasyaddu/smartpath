<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

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
        return view('Auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    // Update password di database
    public function resetPassword(Request $request)
    {
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

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Kata sandi berhasil diperbarui! Silakan login.')
            : back()->withErrors(['email' => __($status)]);
    }
}

