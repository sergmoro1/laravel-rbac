<?php

namespace Sergmoro1\Rbac\Console\Commands;

use Illuminate\Console\Command;
use Yiisoft\Rbac\Manager;
use Yiisoft\Rbac\Permission;
use Yiisoft\Rbac\Role;
use Yiisoft\Rbac\Php\AssignmentsStorage;
use Yiisoft\Rbac\Php\ItemsStorage;
use Yiisoft\Rbac\Rules\Container\RulesContainer;
use Yiisoft\Access\AccessCheckerInterface;
use Sergmoro1\Rbac\Commands\Rbac\Rules\AdminOrOwnerRule;
use App\Models\User;

class Rbac extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbacexample:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Init Role Based Access Control';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(private AccessCheckerInterface $accessChecker)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $manager = $this->accessChecker;

       // Post
        $manager->addPermission(new Permission('createPost'));
        $manager->addPermission((new Permission('updatePost'))->withRuleName(AdminOrOwnerRule::class));
        $manager->addPermission((new Permission('deletePost'))->withRuleName(AdminOrOwnerRule::class));

        // Roles
        $manager->addRole(new Role('author'));
        $manager->addRole(new Role('admin'));

        // author
        $manager->addChild('author', 'createPost');
        $manager->addChild('author', 'updatePost');
        $manager->addChild('author', 'deletePost');

        // the admin can do everything the author can
        $manager->addChild('admin', 'author');

        // assign users for their roles
        foreach (User::get() as $user) {
            $role = $user->email == 'john-doe@sample.com' ? User::ROLE_ADMIN : User::ROLE_AUTHOR;
            $manager->assign($role, $user->id);
        }

        return 0;
    }
}
