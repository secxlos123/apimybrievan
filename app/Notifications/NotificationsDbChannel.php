<?php

/**
  * Send the given notification.
  *
  * @param  mixed  $notifiable
  * @param  \Illuminate\Notifications\Notification  $notification
  * @return \Illuminate\Database\Eloquent\Model
  */

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class NotificationsDbChannel
{

    public function send($notifiable, Notification $notification)
    {
        \Log::info('==========================MASUK=================================================');
        $data = $notification->toDatabase($notifiable);
        \Log::info($data);
        if (isset($data['prop_id'])) {
            return $notifiable->routeNotificationFor('database')->create([
                // 'id' => $notification->id,
                    //customize here
                'branch_id'=> $data['prop_id'],
                    //'role_name'=> $data['role'],
                // 'eform_id'=> $data['eform_id'],
                    //field existing
                'type' => get_class($notification),
                'data' => $data,
                'read_at' => null,
                ]);
        }else{
            return $notifiable->routeNotificationFor('database')->create([
                'id' => $notification->id,
                    //customize here
                'branch_id'=> $data['branch_id'],
                    //'role_name'=> $data['role'],
                'eform_id'=> $data['eform_id'],
                    //field existing
                'type' => get_class($notification),
                'data' => $data,
                'read_at' => null,
                ]);
        }
    }

}