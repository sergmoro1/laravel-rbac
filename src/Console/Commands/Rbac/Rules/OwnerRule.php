<?php

namespace App\Console\Commands\Rbac\Rules;

use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\RuleContext;
use Yiisoft\Rbac\RuleInterface;

/**
 * Checks if current user.id matches user_id passed via params.
 */
class OwnerRule implements RuleInterface
{
    public function execute(?string $userId, Item $item, RuleContext $context): bool
    {
        $entity_user_id = (string) $context->getParameterValue('user_id');
        return $entity_user_id === $userId;
    }
}
