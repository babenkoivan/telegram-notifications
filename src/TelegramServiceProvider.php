<?php

namespace TelegramNotifications;

use Illuminate\Support\ServiceProvider;

class TelegramServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    private $configPath;

    public function __construct($app)
    {
        parent::__construct($app);

        $this->configPath = dirname(__DIR__) . '/config/telegram.php';
    }

    public function register()
    {
        $this->mergeConfigFrom($this->configPath, basename($this->configPath, '.php'));
    }

    public function boot()
    {
        $this->publishes([
            $this->configPath => config_path(basename($this->configPath)),
        ]);
    }
}
