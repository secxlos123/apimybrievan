<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\NotificationsDbChannel;
use App\Models\Developer;
use App\Models\ApprovalDataChange;

class RejectDeveloperProfile extends Notification
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
        $approvalDataChange = ApprovalDataChange::where('related_id',$this->dev->id)->whereNull('approval_by')->first();
        if(empty($approvalDataChange)){
            $approvalDataChange = ApprovalDataChange::where('related_id',$this->dev->id)->first(); 
        }
        return [
            'developer_id' => $this->dev->id,
            'user_id' => $notifiable->id,
            'approval_data_changes_id' => $approvalDataChange->id,
            'city_id' => $approvalDataChange->city_id,            
            'company_name' => $approvalDataChange->company_name,            
            'user_name' => $notifiable->first_name.' '.$notifiable->last_name,
            'branch_id' => 0,
            'role_name' => $notifiable->roles->first()->slug,
            'slug' => $this->dev->id,
            'type_module' => $typeModule,
            'created_at' => $this->dev->created_at,
        ];
    }
}
