<?php

namespace App\Notifications;

use App\Models\Company;
use App\Models\CompanyMembership;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CompanyMemberRemovedNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Company $company,
        protected CompanyMembership $membership,
        protected ?User $actor = null,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $company = $this->company->fresh('owner');

        return (new MailMessage)
            ->subject("You were removed from {$company->name}")
            ->greeting("Hello {$notifiable->name},")
            ->line('Your company membership has been ended.')
            ->line('Company: ' . $company->name)
            ->line('Slug: ' . $company->slug)
            ->line('Industry: ' . ($company->industry ?? 'N/A'))
            ->line('Company size: ' . ($company->company_size ?? 'N/A'))
            ->line('Founded year: ' . ($company->founded_year ?? 'N/A'))
            ->line('Location: ' . ($company->location ?? 'N/A'))
            ->line('Last role: ' . $this->membership->company_role)
            ->line('Last position: ' . $this->membership->position)
            ->line('Last information: ' . ($this->membership->information ?? 'N/A'))
            ->line('Start date: ' . ($this->membership->start_date?->toDateString() ?? 'N/A'))
            ->line('End date: ' . ($this->membership->end_date?->toDateString() ?? 'N/A'))
            ->line('Was current position: ' . ($this->membership->is_current_position ? 'Yes' : 'No'))
            ->line('Verified: ' . ($company->is_verified ? 'Yes' : 'No'))
            ->line('Created by: ' . ($company->owner?->name ?? 'N/A'))
            ->line('Removed by: ' . ($this->actor?->name ?? $company->owner?->name ?? 'System'))
            ->line('If you believe this was a mistake, contact the company owner or admin.');
    }
}