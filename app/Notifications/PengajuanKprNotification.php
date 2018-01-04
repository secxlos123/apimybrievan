<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\CustomDbChannel;
use App\Models\EForm;
class PengajuanKprNotification extends Notification
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
        //return ['database'];
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
        $data = EForm::findOrFail($this->eForm->id);
        return [
            'eform_id' => $this->eForm->id,
            'user_id' => $notifiable->id,
            'user_name' => $notifiable->first_name.' '.$notifiable->last_name,
            'nik' => $this->eForm->nik,
            'ref_number' => $this->eForm->ref_number,
            'branch_id' => $data->branch_id,
            'created_at' => $this->eForm->created_at,
        ];
    }
}
