<?php

namespace App\Notifications;

use App\Models\FamilyInvitation as Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class FamilyInvitation extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Invitation $invitation,
        private readonly string $token,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = URL::temporarySignedRoute('family.invitations.show', $this->invitation->expires_at, [
            'invitation' => $this->invitation,
            'token' => $this->token,
        ]);

        return (new MailMessage)
            ->subject('Undangan Bergabung Keluarga - '.config('app.name'))
            ->greeting('Halo '.$notifiable->name.'!')
            ->line('Anda diundang bergabung dengan '.$this->invitation->family->name.'.')
            ->line('Data pribadi Anda yang lama tidak digabung atau dipindahkan.')
            ->action('Lihat Undangan', $url)
            ->line('Tautan ini berlaku hingga '.$this->invitation->expires_at->translatedFormat('d F Y H:i').'.')
            ->line('Jika Anda tidak merasa diundang, abaikan email ini.');
    }
}
