<?php

namespace Squadron\User\Services;

use Illuminate\Support\Str;

/** @noinspection PhpHierarchyChecksInspection */
class PasswordBrokerManager extends \Illuminate\Auth\Passwords\PasswordBrokerManager
{
    /**
     * {@inheritdoc}
     */
    protected function createTokenRepository(array $config)
    {
        $key = $this->app['config']['app.key'];

        if (Str::startsWith($key, 'base64:'))
        {
            $key = base64_decode(substr($key, 7));
        }

        $connection = $config['connection'] ?? null;

        return new DatabaseTokenRepository(
            $this->app['db']->connection($connection),
            $this->app['hash'],
            $config['table'],
            $key,
            $config['expire']
        );
    }
}
