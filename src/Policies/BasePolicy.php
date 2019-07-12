<?php

namespace Squadron\User\Policies;

use Squadron\User\Models\User;
use Illuminate\Database\Eloquent\Model;

abstract class BasePolicy
{
    /**
     * @param User   $currentUser
     * @param string $ability
     * @param Model  $model
     *
     * @return bool|null
     */
    public function before(User $currentUser, string $ability, $model): ?bool
    {
        if ($currentUser->isRoot())
        {
            $deletingUser = $ability === 'delete' && $model instanceof User;

            if ($deletingUser)
            {
                // root can't delete himself
                return $model->getKey() !== $currentUser->getKey();
            }

            return true;
        }

        return null;
    }
}
