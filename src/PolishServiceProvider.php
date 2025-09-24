<?php

namespace Hdaklue\Polish;

use Illuminate\Support\ServiceProvider;
use Hdaklue\Polish\Console\Commands\MakePolisherCommand;

class PolishServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakePolisherCommand::class,
            ]);
        }
    }
}