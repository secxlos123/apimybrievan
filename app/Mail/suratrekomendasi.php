<?php

namespace App\Mail;

use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class suratrekomendasi extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
//	public $mail;
    public function __construct()
    {
//		   $this->order = $order;
 //          $this->mail = $mail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
		$file = storage_path('app/PDF/Konversi.pdf');
		   $this->view('mails.suratrekomendasi')->attach($file, [
        'as' => 'Surat Rekomendasi',
        'mime' => 'application/pdf',
    ]);;
 //       if (env('APP_ENV') == 'production') {
 //           return $this->view( 'mails.example', $this->mail );
 //       }

  //      return $this->view( 'mails.example', $this->mail );
    }
}
