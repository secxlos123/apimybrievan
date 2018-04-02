<?php

namespace App\Transformers;

use App\Models\UserNotification;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class NotificationTransformer extends TransformerAbstract
{
    public function transform(UserNotification $notification)
    {
        return [
            "id" => $notification->id
            , "subject" => "Pengajuan KPR"
            , "username" => ( @$notification->notifiable->username ) ? @$notification->notifiable->username : ""
            , "data" => $notification->data
            , "created_at" => $notification->created_at->diffForHumans()
            , "is_read" => ( bool ) $notification->is_read
            , "read_at" => Carbon::parse( $notification->read_at )->format( "Y-m-d H:i:s" )
        ];
    }
}