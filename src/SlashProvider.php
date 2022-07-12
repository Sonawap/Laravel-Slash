<?php

namespace Sonawap\Slash;

use Illuminate\Support\ServiceProvider;

class SlashProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
