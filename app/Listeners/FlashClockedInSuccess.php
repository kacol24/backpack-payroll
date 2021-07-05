<?php

namespace App\Listeners;

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

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        \Alert::success('Berhasil memulai shift!')->flash();
    }
}
