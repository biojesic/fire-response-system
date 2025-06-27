<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BarangayRejectedNotification extends Notification
{
    use Queueable;

    public $reason;
    public $canReapply;
    public $reapplyUrl;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $reason, bool $canReapply)
    {
        $this->reason = $reason;
        $this->canReapply = $canReapply;
        $this->reapplyUrl = route('barangay.reapply');
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Barangay Application Rejected')
            ->greeting('Hello ' . $notifiable->userFirstName . ',')
            ->line('Your barangay application has been reviewed and unfortunately rejected.')
            ->line('**Reason**: ' . $this->reason)
            ->line($this->canReapply ? 
                'You may reapply after 7 days.' : 
                'This decision is final.')
            ->action($this->canReapply ? 'Reapply Now' : 'View Portal', $this->reapplyUrl)
            ->line('Thank you for using our system.');
    }

    /**
     * Get the array representation for database storage.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Barangay application rejected',
            'reason' => $this->reason,
            'reapply_url' => $this->canReapply ? $this->reapplyUrl : null,
            'rejected_at' => now()->toDateTimeString(),
        ];
    }
}