<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as Base;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Base
{
    public function toMail($notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Reset Your ONYX Legal Password')
            ->view('emails.reset-password', [
                'user'      => $notifiable,
                'resetUrl'  => $url,
                'expiresIn' => config('auth.passwords.users.expire', 60),
            ]);
    }
}
