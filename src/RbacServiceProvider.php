<?php

namespace Sergmoro1\Rbac;

use Illuminate\Support\ServiceProvider;

class RbacServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/Console/Commands' => base_path('Console/Commands'),
            ]);
        }
    }
}