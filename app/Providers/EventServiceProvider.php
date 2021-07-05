<?php

namespace App\Providers;

use App\Events\EmployeeClockedIn;
use App\Events\EmployeeClockedOut;
use App\Listeners\FlashClockedInSuccess;
use App\Listeners\FlashClockedOutSuccess;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        EmployeeClockedIn::class => [
            FlashClockedInSuccess::class,
        ],

        EmployeeClockedOut::class => [
            FlashClockedOutSuccess::class,
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
        //
    }
}
