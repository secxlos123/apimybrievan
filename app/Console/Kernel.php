<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        '\App\Console\Commands\SchedulerRekening'
        , '\App\Console\Commands\BackupDatabaseCommand'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        if ( env( 'BACKUP_DATABASE', false ) ) {
            $schedule->command('db:backup daily')
                ->dailyAt('00:00');
            $schedule->command('db:backup monthly')
                ->monthlyOn(1, '00:00');
        }

        $schedule->command('SchedulerRekening:updaterekening')
            ->weekdays()
            ->hourlyAt('12');

        // // test function
        // $schedule->call(function(){
        //     \Log::info("salah masuk");
        // })->everyMinute();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
