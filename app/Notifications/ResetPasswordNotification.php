<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    /**
     * Build the reset password mail message in Indonesian.
     */
    protected function buildMailMessage($url): MailMessage
    {
        $expire = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

        return (new MailMessage)
            ->subject('Atur Ulang Password - '.config('app.name'))
            ->greeting('Halo!')
            ->line('Anda menerima email ini karena ada permintaan untuk mengatur ulang password akun Anda.')
            ->action('Atur Ulang Password', $url)
            ->line("Tautan ini berlaku selama {$expire} menit.")
            ->line('Jika Anda tidak merasa melakukan permintaan ini, abaikan saja email ini.');
    }
}
