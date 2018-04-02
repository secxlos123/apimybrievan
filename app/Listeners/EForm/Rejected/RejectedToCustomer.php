<?php

namespace App\Listeners\EForm\Rejected;

use App\Events\EForm\RejectedEform;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Support\Facades\Mail;
use App\Mail\RejectedEformCustomer;

class RejectedToCustomer
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
     * @param  Rejected  $event
     * @return void
     */
    public function handle( RejectedEform $event)
    {
        $eform = $event->eform;
        $customer = $eform->customer;

        $mail = [
            "email" => $customer->email
            , "name" => $customer->fullname
            , "ref_number" => $eform->ref_number
        ];

        Mail::to( $mail["email"] )->send( new RejectedEformCustomer( $mail ) );
    }
}
