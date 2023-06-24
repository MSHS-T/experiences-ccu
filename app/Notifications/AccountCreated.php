<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class AccountCreated extends ResetPassword
{
    /**
     * Get the reset password notification mail message for the given URL.
     *
     * @param  string  $url
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    protected function buildMailMessage($url)
    {
        return (new MailMessage)
            ->subject(__('mail.account_created.subject'))
            ->line(__('mail.account_created.line1'))
            ->action(__('mail.account_created.action'), $url)
            ->line(__('mail.account_created.line2', ['count' => config('auth.passwords.' . config('auth.defaults.passwords') . '.expire')]));
    }
}
