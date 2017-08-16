<?php

namespace App\Listeners\Customer;

use App\Events\Customer\CustomerRegistered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Support\Facades\Mail;
use App\Mail\Registered;

class SendMailNotification
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
    public function handle( CustomerRegistered $event )
    {
        $mail = [
            'name' => $event->user->fullname,
            'email' => $event->user->email,
            'password' => $event->password
        ];
        
        Mail::to( $mail[ 'email' ] )->send( new Registered( $mail ) );
    }
}
