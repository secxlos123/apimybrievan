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
        return $notifiable->routeNotificationFor('database')->create([
            'id' => $notification->id,
            'branch_id'=> $data['branch_id'],
            'role_name'=> 'customer',
            'slug'=> $data['slug'],
            'type' => get_class($notification),
            'data' => $data,
            'read_at' => null,
            'type_module'=> $data['type_module'],
            'is_read' => null,
            ]);
    }
}