<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordBase;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPasswordBase implements ShouldQueue
{
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Reset Password Notification')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', url(config('app.frontend_url') . '/reset-password?token=' . $this->token . '&email=' . $notifiable->getEmailForPasswordReset()))
            ->line('This password reset link will expire in ' . config('auth.passwords.' . config('auth.defaults.passwords') . '.expire') . ' minutes.')
            ->line('If you did not request a password reset, no further action is required.');
    }
}