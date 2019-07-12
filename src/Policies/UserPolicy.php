<?php

namespace Squadron\User\Policies;

use Squadron\CRUD\Policies\Contracts\CRUDPolicyContract;

class UserPolicy extends BasePolicy implements CRUDPolicyContract
{
    public function getList($currentUser): bool
    {
        return false;
    }

    public function getSingle($currentUser, $user): bool
    {
        return $currentUser->getKey() === $user->getKey();
    }

    public function create($currentUser): bool
    {
        return false;
    }

    public function update($currentUser, $user): bool
    {
        return $currentUser->getKey() === $user->getKey();
    }

    public function delete($currentUser, $user): bool
    {
        return false;
    }
}
