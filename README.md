# Laravel RBAC
This package adapts [Yii Role-Based Access Control](https://github.com/yiisoft/rbac) that is used in Yii Framework to Laravel.

## Installation
```
composer require sergmoro1/laravel-rbac
```

## Bind `AccessCheckerInterface` with its implimentation
```
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Yiisoft\Access\AccessCheckerInterface;
use Yiisoft\Rbac\Manager;
use Yiisoft\Rbac\SimpleRuleFactory;
use Yiisoft\Rbac\Php\AssignmentsStorage;
use Yiisoft\Rbac\Php\ItemsStorage;
use Yiisoft\Rbac\Rules\Container\RulesContainer;

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
        $this->app->bind(AccessCheckerInterface::class, function ($app) {
            $directory = __DIR__ . '/../../storage/rbac';

            $itemsStorage = new ItemsStorage($directory . '/items.php');
            $assignmentsStorage = new AssignmentsStorage($directory . '/assignments.php');
            $rulesContainer = new RulesContainer(app());
    
            return new Manager($itemsStorage, $assignmentsStorage, $rulesContainer);
        });
    }
}
```
## 
## Tests
```
composer test
```
