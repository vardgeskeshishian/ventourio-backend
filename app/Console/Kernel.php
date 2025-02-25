<?php

namespace App\Console;

use App\Services\System\DeleteExpiredDiscounts;
use App\Services\System\DispatchExternalBookingsStatusUpdate;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        # Обновляем переходящие статусы у внешних бронирований
        $schedule->call(new DispatchExternalBookingsStatusUpdate)->everyThirtyMinutes();

        # Удаляем скидки с истекшим сроком. Обновляем связанные сущности
        $schedule->call(new DeleteExpiredDiscounts)->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
