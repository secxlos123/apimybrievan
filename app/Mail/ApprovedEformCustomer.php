<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ApprovedEformCustomer extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * This data for send mail.
     *
     * @var array
     */
    public $mail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct( array $mail )
    {
        $this->mail = $mail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if (env('APP_ENV') == 'production') {
            return $this->view( 'mails.approved_eform_customer_simple', $this->mail );
        }
        return $this->view( 'mails.approved_eform_customer', $this->mail );
    }
}
