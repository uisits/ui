<?php

namespace Uisits\Ui;

use Uisits\Ui\Commands\InstallCommand;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

class UiServiceProvider extends ServiceProvider implements DeferrableProvider
{

    /**
     *
     */
    public function boot()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([InstallCommand::class]);
    }

    /**
     *
     */
    public function register()
    {

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Commands\InstallCommand::class];
    }
}
