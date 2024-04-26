<?php

namespace App\Console\Commands\Rbac\Rules;

use Yiisoft\Rbac\RuleInterface;
use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\RuleContext;
use Yiisoft\Rbac\CompositeRule;
use App\Console\Commands\Rbac\Rules\AdminRule;
use App\Console\Commands\Rbac\Rules\OwnerRule;

/**
 * Composite rule - user is admin or entity owner.
 */
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
