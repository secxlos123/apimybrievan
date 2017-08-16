<?php

namespace App\Jobs;

use App\Models\User;
use App\Mail\ResetPassword;
use App\Mail\Registered;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendPasswordEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The variable has instance of App\Models\User.
     *
     * @var array
     */
    public $user;

    /**
     * This unique password.
     *
     * @var string
     */
    public $password;

    /**
     * This unique password.
     *
     * @var string
     */
    public $type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, $password, $type)
    {
        $this->user = $user;
        $this->password = $password;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mail = ['email' => $this->user->email, 'password' => $this->password, 'name' => $this->user->fullname];
        $send = Mail::to($mail['email']);

        switch ($this->type) {
            case 'reset': return $send->send(new ResetPassword($mail));
            case 'registered': return $send->send(new Registered($mail));
        }
    }
}
