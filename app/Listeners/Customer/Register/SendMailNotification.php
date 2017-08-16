<?php

namespace App\Listeners\Customer\Register;

use App\Events\Customer\CustomerRegister;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Support\Facades\Mail;
use App\Mail\Register;

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
     * @param  CustomerRegister  $event
     * @return void
     */
    public function handle( CustomerRegister $event )
    {
        $mail = [
            'email' => $event->user->email
        ];
        
        Mail::to( $mail[ 'email' ] )->send( new Register( $mail ) );
    }
}
