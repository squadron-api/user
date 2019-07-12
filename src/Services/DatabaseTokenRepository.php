<?php

namespace Squadron\User\Services;

use Illuminate\Support\Str;

class DatabaseTokenRepository extends \Illuminate\Auth\Passwords\DatabaseTokenRepository
{
    /**
     * {@inheritdoc}
     */
    public function createNewToken(): string
    {
        $tokenSettings = config('squadron.user.restore.token');
        $tokenBlocks = [];

        for ($i = 0; $i < $tokenSettings['blocksCount']; ++$i)
        {
            $tokenBlocks[] = Str::random($tokenSettings['blockCharactersCount']);
        }

        return Str::upper(implode($tokenSettings['delimiter'], $tokenBlocks));
    }
}
