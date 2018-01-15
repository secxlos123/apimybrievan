<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\NotificationsDbChannel;
use App\Models\Appointment;
use App\Models\EForm;

class NewSchedulerCustomer extends Notification
{
    use Queueable;

    public $appointment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($appointment)
    {
        $this->appointment   = $appointment;
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
       //
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
     public function toArray($notifiable)
    {
        //
    }

    public function toDatabase($notifiable)
    {
        $data = EForm::findOrFail($this->appointment->eform_id);
        return [
            'appointment_id' => $this->appointment->id,
            'eform_id' => $this->appointment->eform_id,
            'user_id' => $notifiable->id,
            'user_name' => $notifiable->first_name.' '.$notifiable->last_name,
            'nik' => $data->nik,
            'ref_number' => $data->ref_number,
            'branch_id' => $data->branch_id,
            'type_module' => 'schedule',
            'created_at' => $this->appointment->created_at,
        ];
    }
}
