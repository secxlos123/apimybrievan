<?php

namespace App\Listeners\Customer;

use App\Events\Customer\CustomerVerify;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationEFormCustomer;

class VerifyMailNotification
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
     * @param  CustomerVerify   $event
     * @return void
     */
    public function handle( CustomerVerify $event )
    {
        $customer = $event->customer;
        $eform = $event->eform;
        $mail = [
            'email' => $customer->email,
            'name' => $customer->fullname,
            'url' => env( 'MAIN_APP_URL', 'https://mybri.stagingapps.net' ) . '/eform/' . $form->token;
            'eform' => $eform,
            'customer' => $customer
        ];
        
        Mail::to( $mail[ 'email' ] )->send( new VerificationEFormCustomer( $mail ) );
    }
}
