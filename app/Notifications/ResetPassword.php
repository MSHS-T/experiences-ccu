<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class ResetPassword extends \Illuminate\Auth\Notifications\ResetPassword
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        $url = url(config('app.url') . 'resetpassword?token=' . $this->token . '&email=' . $notifiable->getEmailForPasswordReset(), false);

        return (new MailMessage)
            ->subject(Lang::get('Réinitialisation de votre mot de passe'))
            ->line(Lang::get('Vous recevez cet email car une demande de réinitialisation de votre mot de passe a été faite.'))
            ->action(Lang::get('Réinitialiser mon mot de passe'), $url)
            ->line(Lang::get('Ce lien expirera dans :count minutes.', ['count' => config('auth.passwords.' . config('auth.defaults.passwords') . '.expire')]))
            ->line(Lang::get('Si vous n\'avez pas demandé de réinitialisation de mot de passe, ne tenez pas compte de cet email.'));
    }
}
