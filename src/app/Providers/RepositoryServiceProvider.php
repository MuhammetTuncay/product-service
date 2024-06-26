<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {

    }

    public function boot()
    {
        $this->app->bind(
            \App\Repositories\ProductRepository::class,
            \App\Repositories\Eloquent\ProductRepositoryEloquent::class
        );
    }
}
