<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Ensure queue worker runs continuously via Laravel scheduler (ideal for cPanel cron)
Schedule::command('queue:work --max-time=55 --sleep=2')->everyMinute()->withoutOverlapping();
