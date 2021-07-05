<?php

namespace App\Listeners;

use App\Models\Attendance;
use App\Notifications\EmployeeAttendance;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\Pushbullet\Targets\Email;

class FlashClockedOutSuccess
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
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if (! $event->pushbullet) {
            \Alert::success('Berhasil akhiri shift!')->flash();
        } else {
            $notification = new EmployeeAttendance($event->attendance, Attendance::TYPE_CLOCK_OUT);

            Notification::route('pushbullet', new Email(config('services.pushbullet.super_email')))
                        ->notify($notification);
        }
    }
}
