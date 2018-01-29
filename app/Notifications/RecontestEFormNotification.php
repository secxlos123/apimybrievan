<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\EForm;

class RecontestEFormNotification extends Notification
{
    use Queueable;

    public $eForm;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($eForm)
    {
        $this->eForm   = $eForm;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [NotificationsDbChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        /*return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');*/
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        $typeModule = getTypeModule(EForm::class);
        $message    = getMessage("eform_recontest");
        return [
            'eform_id' => $this->eForm->id,
            'user_id' => $notifiable->id,
            'user_name' => $notifiable->first_name.' '.$notifiable->last_name,
            'nik' => $this->eForm->nik,
            'ref_number' => $this->eForm->ref_number,
            'branch_id' => $this->eForm->branch_id,
            'slug' => $this->eForm->id,
            'type_module' => $typeModule,
            'created_at' => $this->eForm->created_at,
            'role_name' => $notifiable->roles->first()->slug,
            'message' => $message,
        ];
    }
}