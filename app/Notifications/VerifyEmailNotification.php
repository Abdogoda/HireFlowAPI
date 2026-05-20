<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends VerifyEmailBase implements ShouldQueue
{
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verify Email Address')
            ->line('Please click the button below to verify your email address.')
            ->action('Verify Email Address', $verificationUrl)
            ->line('If you did not create an account, no further action is required.');
    }
}