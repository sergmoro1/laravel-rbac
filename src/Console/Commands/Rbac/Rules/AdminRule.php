<?php

namespace App\Console\Commands\Rbac\Rules;

use Illuminate\Support\Facades\Auth;
use Yiisoft\Access\AccessCheckerInterface;
use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\RuleContext;
use Yiisoft\Rbac\RuleInterface;
use App\Models\User;
 
/**
 * Checks if current user is admin.
 */
class AdminRule implements RuleInterface
{
    /**
     * @param AccessCheckerInterface $accessChecker RBAC manager.
     */
    public function __construct(private AccessCheckerInterface $accessChecker)
    {
    }
    
    public function execute(?string $userId, Item $item, RuleContext $context): bool
    {
        $roles = array_keys($this->accessChecker->getRolesByUserId($userId));
        if (in_array(User::ROLE_ADMIN, $roles)) {
            return true;
        }
        
        return false;
    }
}
