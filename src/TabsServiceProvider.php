<?php

declare(strict_types=1);

namespace BBSLab\Tabs;

use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class TabsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Nova::serving(function (ServingNova $event): void {
            Nova::script('tabs', __DIR__.'/../dist/js/field.js');
            Nova::style('tabs', __DIR__.'/../dist/css/field.css');
        });
    }

    /**
     * Register any application services.
     */
    public function register(): void {}
}
