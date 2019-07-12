<?php

namespace Squadron\User\Tests;

use Squadron\User\ServiceProvider;

class TestCase extends \Squadron\Tests\TestCase
{
    protected function getServiceProviders(): array
    {
        return [ServiceProvider::class];
    }
}
