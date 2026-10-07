<?php

namespace App\Listeners;

use App\Events\StudentRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendWelcomeEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(StudentRegistered $event): void
    {
        $student = $event->student;

        // Log the email action
        Log::info("Welcome email sent to student: {$student->name} ({$student->email})");
    }
}