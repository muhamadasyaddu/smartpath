<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($object)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $namaUser = $notifiable->nama_lengkap ?: 'Pengguna SmartPath';

        return (new MailMessage)
            ->subject('Reset Kata Sandi - SmartPath')
            ->greeting('Halo, ' . $namaUser . '!')
            ->line('Kami menerima permintaan untuk mereset kata sandi pada akun SmartPath Anda.')
            ->line('Silakan klik tombol di bawah ini untuk melanjutkan proses pembaruan kata sandi:')
            ->action('Reset Kata Sandi Sekarang', $url)
            ->line('Tautan ini hanya berlaku selama 60 menit demi keamanan akun Anda.')
            ->line('Jika Anda tidak melakukan permintaan ini, abaikan saja email ini dan kata sandi Anda tidak akan berubah.')
            ->salutation("Salam hangat,\nTim Pengembang SmartPath");
    }
}