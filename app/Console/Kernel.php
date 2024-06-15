<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Schedule your command to run daily
        // $schedule->command('command:daily-reset')->daily()->at('02:30');
        $schedule->command('firedata:update')->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        
        $this->load(__DIR__.'/Commands/UpdateFireData.php');
     

        
    
        require base_path('routes/console.php');
    }
}
