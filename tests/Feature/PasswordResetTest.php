<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\CustomResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_request_and_complete_a_password_reset(): void
    {
        Notification::fake();

        $user = User::query()->create([
            'nama_lengkap' => 'Pengguna Uji',
            'email' => 'reset@example.test',
            'kata_sandi' => Hash::make('password-lama'),
            'peran' => 'warga',
        ]);

        $this->post(route('password.email'), [
            'email' => $user->email,
        ])->assertSessionHas('status');

        $token = null;

        Notification::assertSentTo(
            $user,
            CustomResetPasswordNotification::class,
            function (CustomResetPasswordNotification $notification) use (&$token): bool {
                $token = $notification->token;

                return true;
            }
        );

        $this->get(route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]))->assertOk();

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'password-baru',
            'password_confirmation' => 'password-baru',
        ])->assertRedirect(route('login'))
            ->assertSessionHas('status');

        $this->assertTrue(
            Hash::check('password-baru', $user->fresh()->kata_sandi)
        );
    }
}
