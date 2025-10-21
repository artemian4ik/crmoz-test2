<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\GetItemsTask::class,
        \App\Console\Commands\GetCustomerTask::class,
        \App\Console\Commands\GetSalesOrderTask::class,
        \App\Console\Commands\Test::class
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('app:get-items-task')->hourly();
        $schedule->command('app:get-customer-task')->hourly();
    }

    protected function commands()
    {
        require base_path('routes/console.php');
    }
}