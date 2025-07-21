<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class VerifyEmailFrench extends VerifyEmail
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject(Lang::get('Vérification de l\'adresse e-mail'))
            ->line(Lang::get('Veuillez cliquer sur le bouton ci-dessous pour vérifier votre adresse e-mail.'))
            ->action(Lang::get('Vérifier l\'adresse e-mail'), $verificationUrl)
            ->line(Lang::get('Si vous n\'avez pas créé de compte, aucune action supplémentaire n\'est requise.'));
    }
}
