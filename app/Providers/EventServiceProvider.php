<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Customer\CustomerRegister' => [
            'App\Listeners\Customer\Register\SendMailNotification',
        ],
        'App\Events\Customer\CustomerRegistered' => [
            'App\Listeners\Customer\SendMailNotification',
        ],
        'App\Events\Customer\CustomerVerify' => [
            'App\Listeners\Customer\VerifyMailNotification',
        ],
        'App\Events\EForm\Approved' => [
            'App\Listeners\EForm\Approved\MailNotificationToCustomer',
        ],
        'App\Events\EForm\VerifyEForm' => [
            'App\Listeners\EForm\VerifyEFormCustomer',
        ],
        'App\Events\Developer\CreateOrUpdate' => [
            'App\Listeners\Developer\CreateOrUpdate',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        foreach (config('app.observers') as $model => $observer) {
            $model::observe($observer);
        }
    }
}
