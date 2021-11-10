<?php

namespace App\Providers;

use App\Models\People;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        $this->app->bind('people.types', function () {
            return collect([
                People::TYPE_ACADEMIC,
                People::TYPE_PGR,
            ]);
        });
    }
}
