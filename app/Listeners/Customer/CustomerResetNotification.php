<?php

namespace App\Listeners\Customer;

use App\Events\Customer\CustomerReset;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPassword;

class CustomerResetNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  CustomerRegistered  $event
     * @return void
     */
    public function handle( CustomerReset $event )
    {
        $mail = [
            "name" => $event->customer->fullname
            , "email" => $event->customer->email
            , "password" => $event->password
            , "created_at" => $event->customer->created_at
        ];

        Mail::to( $mail["email"] )->send( new ResetPassword( $mail ) );
    }
}
