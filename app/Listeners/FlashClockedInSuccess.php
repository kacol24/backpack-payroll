<?php

namespace App\Listeners;

use App\Models\Attendance;
use App\Notifications\EmployeeAttendance;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\Pushbullet\Targets\Email;

class FlashClockedInSuccess
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

    public function handle($event)
    {
        if (! $event->pushbullet) {
            \Alert::success('Berhasil memulai shift!')->flash();
        } else {
            $notification = new EmployeeAttendance($event->attendance, Attendance::TYPE_CLOCK_IN);

            Notification::route('pushbullet', new Email(config('services.pushbullet.super_email')))
                        ->notify($notification);
        }
    }
}
