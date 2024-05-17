# Laravel RBAC
This package adapts [Yii Role-Based Access Control](https://github.com/yiisoft/rbac) with [PHP storage](https://github.com/yiisoft/rbac-php) to Laravel.

## Installation
```
composer require sergmoro1/laravel-rbac
```

## Publish RBAC sample
```
php artisan vendor:publish --tag=rbac-sample
```
After sample publishing the class `App\Concole\Commands\Rbac` can be used as the basis of your own access system.

## Bind `AccessCheckerInterface` with its implimentation
Make changes to the file `App\Providers\AppServiceProvider`.
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

## RBAC init
Before executing the command below, edit the assignment of roles to users in the class `App\Console\Commands\Rbac`.
```
php artisan rbac:init
```

## Example
The example implies that you have a `Post` model defined. The author can add posts and edit only his own. The administrator can also add posts, but can edit all. Make some changes to the classes below.

### `User` model
```
    const ROLE_ADMIN  = 'admin';
    const ROLE_AUTHOR = 'author';
```

### `Controller`
```
<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Yiisoft\Access\AccessCheckerInterface;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct(private AccessCheckerInterface $accessChecker)
    { 
    }

    /**
     * Checking the permission to perform the action.
     * 
     * @param string $action
     * @param array $params
     */
    protected function checkAccess(string $action, $params = [])
    {
        $userId = auth()->id();
        if (!$this->accessChecker->userHasPermission($userId, $action, $params)) {
            abort(403, 'Access denied');
        }
    }
}
```

### `PostController`
```
<?php

namespace App\Http\Controllers;

class PostController extends Controller
{
    public function create()
    {
        $this->checkAccess('createPost');
        
        $post = new Post();
        $post->status = Post::STATUS_DRAFT;

        return view('post', ['post' => $post, 'action' => 'create']);
    }

    public function edit(int $id)
    {
        $post = Post::find($id);

        $this->checkAccess('updatePost', ['user_id' => $post->user_id]);
        
        return view('post', ['post' => $post, 'action' => 'edit']);
    }    

```

## Rules
Pay attention composite rule used in a Rbac command.
```
        $manager->addPermission((new Permission('updatePost'))->withRuleName(AdminOrOwnerRule::class));
```

In this way, complex rules for verifying the rights to perform actions can be set.
```
<?php

namespace App\Console\Commands\Rbac\Rules;

class AdminOrOwnerRule implements RuleInterface
{
    private $compositeRule;

    public function __construct() 
    {
        $this->compositeRule = new CompositeRule(CompositeRule::OR, [AdminRule::class, OwnerRule::class]);
    }

    public function execute(?string $userId, Item $item, RuleContext $context): bool
    {
        return $this->compositeRule->execute($userId, $item, $context);
    }
}
```