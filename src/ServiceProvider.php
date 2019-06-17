<?php
namespace Motwreen\Translation;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->loadMigrationsFrom(__DIR__.'/migrations');
        $this->loadViewsFrom(__DIR__.'/views', 'Translation');

        $this->publishes([
            __DIR__.'/views' => resource_path('views/vendor/translation'),
            __DIR__.'/migrations' => base_path('database/migrations'),

        ],'motwreen-translation');
    }

    public function register(){}

}
