<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Daftarkan semua Artisan command custom di sini.
     */
    protected $commands = [
        \App\Console\Commands\RedisSubscriberCommand::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // tidak ada scheduled task
    }

    protected function bootstrapWith(array $bootstrappers)
    {
        parent::bootstrapWith($bootstrappers);
    }
}
