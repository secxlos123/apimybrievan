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

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
		$file = storage_path('app/PDF/Surat_Kuasa_Potong_Upah.pdf');
		print_r($file);die();
		$file2 = storage_path('app/PDF/Surat_Rekomendasi_Atasan.pdf');
		   $this->view('mails.suratrekomendasi')->attach($file, [
        'as' => 'Surat Kuasa Potong Gaji',
        'mime' => 'application/pdf',
    ])->attach($file2, [
        'as' => 'Surat Kuasa Potong Gaji',
        'mime' => 'application/pdf',
    ]);
 //       if (env('APP_ENV') == 'production') {
 //           return $this->view( 'mails.example', $this->mail );
 //       }

  //      return $this->view( 'mails.example', $this->mail );
    }
}
