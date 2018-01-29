<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\NotificationsDbChannel;
use App\Models\Collateral;
class CollateralStafRejectOTS extends Notification
{
    use Queueable;
    public $collateral;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($collateral,$branch_id)
    {
        $this->collateral  = $collateral;
        $this->branch_id   = $branch_id;

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

        $typeModule = getTypeModule(Collateral::class);
        $message    = getMessage('collateral_reject_penilaian');
        $data= [
            'collateral_id' => $this->collateral->id,
            'user_id' => $notifiable->id,
            'developer_id' =>  $this->collateral->developer_id,
            'property_id' =>  $this->collateral->property_id,
            'user_name' => $notifiable->first_name.' '.$notifiable->last_name,
            'branch_id' => $this->branch_id,    
            'slug' => $this->collateral->id,
            'type_module' => $typeModule,
            'created_at' => $this->collateral->created_at,
            'role_name' => $notifiable->roles->first()->slug,
            'staff_name' => $this->collateral->staff_name,
            'prop_slug' => $this->collateral->property->slug,
            'message' => $message,
        ];
        return $data;
    }
}
