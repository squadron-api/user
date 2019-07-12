<?php

namespace Squadron\User\Exceptions;

use League\OAuth2\Server\Exception\OAuthServerException;

class SquadronUserException
{
    public static function userDeactivated(): OAuthServerException
    {
        return new OAuthServerException('User account is not active', 6, 'account_inactive', 401);
    }
}
