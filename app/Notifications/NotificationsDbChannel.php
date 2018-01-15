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
                'id' => $notification->id,
                'branch_id'=> $data['prop_id'],
                'role_name'=> 'customer',
                'type' => get_class($notification),
                'data' => $data,
                'read_at' => null,
                'type_module'=> $data['type_module'],
                ]);
        }else{
            return $notifiable->routeNotificationFor('database')->create([
                'id' => $notification->id,
                'branch_id'=> $data['branch_id'],
                'role_name'=> 'customer',
                'eform_id'=> $data['eform_id'],
                'type' => get_class($notification),
                'data' => $data,
                'read_at' => null,
                'type_module'=> $data['type_module'],
                ]);
        }
    }
}