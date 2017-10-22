<?php

namespace App\Listeners\EForm;

use App\Events\EForm\Verify;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Support\Facades\Mail;
use App\Mail\ConfirmationEFormCustomer;

class VerifyEFormCustomer
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
     * @param  Verify  $event
     * @return void
     */
    public function handle( Verify $event )
    {
        $eform = $event->eform;
        $customer = $eform->customer;
        $mail = [
            'email' => $customer->email,
            'name' => $customer->fullname,
            'status' => $eform->status == 'approve' ? 'Setuju' : 'Tidak Setuju'
        ];
        
        Mail::to( $mail[ 'email' ] )->send( new ConfirmationEFormCustomer( $mail ) );
    }
}
