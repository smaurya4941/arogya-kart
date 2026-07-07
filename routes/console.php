<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a simple
| approach to interacting with each command's IO methods.
|
| The scheduler is configured in bootstrap/app.php via withSchedule().
| Scheduled commands registered there:
|
|   pharmacy:stock-alerts  — daily at 08:00 (low-stock & expiry notifications)
|   pharmacy:backup        — daily at 02:00 (gzip mysqldump to storage/app/backups/)
|
| To run the scheduler locally:
|   php artisan schedule:run       (single tick — good for testing)
|   php artisan schedule:work      (loops every minute — good for dev)
|
| To run a queue worker for queued jobs (PDF generation, notifications):
|   php artisan queue:work         (production: run under Supervisor)
|   php artisan queue:work --once  (single job — good for testing)
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
