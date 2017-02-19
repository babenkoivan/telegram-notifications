<?php

namespace TelegramNotifications;

use Illuminate\Support\ServiceProvider;

class TelegramServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/telegram.php' => config_path('telegram.php'),
        ]);
    }
}
