<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\NotificationsDbChannel;
use App\Models\Property;

class PropertyNotification extends Notification
{
    use Queueable;

    public $prop;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($prop)
    {
        $this->prop = $prop;
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
        // return (new MailMessage)
        //             ->line('The introduction to the notification.')
        //             ->action('Notification Action', url('/'))
        //             ->line('Thank you for using our application!');
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

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        $typeModule = getTypeModule(Property::class);

        return [
            'prop_id' => $this->prop->id,
            'user_id' => $notifiable->id,
            'user_name' => $notifiable->first_name.' '.$notifiable->last_name,
            'branch_id' => 0,
            'role_name' => $notifiable->roles->first()->slug,
            'name' => $this->prop->name,
            'slug' => $this->prop->id,
            'type_module' => $typeModule,
        ];
    }
}
