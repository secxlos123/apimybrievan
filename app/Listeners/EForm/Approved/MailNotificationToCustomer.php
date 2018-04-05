<?php

namespace App\Listeners\EForm\Approved;

use App\Events\EForm\Approved;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Support\Facades\Mail;
use App\Mail\ApprovedEformCustomer;

class MailNotificationToCustomer
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
     * @param  Approved  $event
     * @return void
     */
    public function handle( Approved $event )
    {
        $eform = $event->eform;
        $customer = $eform->customer;

        $mail = [
            "email" => $customer->email
            , "name" => $customer->fullname
            , "ref_number" => $eform->ref_number
        ];

        Mail::to( $mail["email"] )->send( new ApprovedEformCustomer( $mail ) );
    }
}
