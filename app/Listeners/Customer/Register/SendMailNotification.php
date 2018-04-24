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
        $activation_code = $event->activation_code;
        $user = $event->user;
        $url = "";
        if ( $activation_code ) {

            $host = env('APP_URL');
            if($host == 'http://103.63.96.167/api/'){
                $url = "http://103.63.96.167/api". "/activate/" . $user->id . "/" . $activation_code;
            }else{
                $url = env("MAIN_APP_URL", "http://mybri.bri.co.id/") . "activate/" . $user->id . "/" . $activation_code;
            }
        }

        $mail = [
            "url" => $url
            , "email" => $user->email
            , "name" => $user->full_name
            , "created_at" => $user->created_at
        ];

        Mail::to( $mail["email"] )->send( new Register( $mail ) );
    }
}
