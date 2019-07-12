<?php

namespace Squadron\User\Events;

use Squadron\User\Models\User;

class TokenCreated
{
    public $tokenData;
    public $user;

    public function __construct(User $user, array $tokenData)
    {
        $this->user = $user;
        $this->tokenData = $tokenData;
    }
}
