<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\NotificationsDbChannel;
use App\Models\Developer;
use App\Models\ApprovalDataChange;

class EditDeveloper extends Notification
{
    use Queueable;

    public $dev;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($dev)
    {
        $this->dev   = $dev;
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
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    public function toDatabase($notifiable)
    {
        $typeModule = getTypeModule(Developer::class);
        
        return [
            'developer_id' => $this->dev->id,
            'user_id' => $notifiable->id,
            'user_name' => $notifiable->first_name.' '.$notifiable->last_name,
            'branch_id' => 0,
            'role_name' => $notifiable->roles->first()->slug,
            'slug' => $this->dev->id,
            'type_module' => $typeModule,
            'created_at' => $this->dev->created_at,
        ];
    }
}
