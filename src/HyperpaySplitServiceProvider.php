<?php

namespace maree\hyperpaySplit;

use Illuminate\Support\ServiceProvider;

class HyperpaySplitServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->publishes([
            __DIR__.'/config/hyperpaySplit.php' => config_path('hyperpaySplit.php'),
        ],'hyperpaySplit');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/hyperpaySplit.php', 'hyperpaySplit'
        );
    }
}
