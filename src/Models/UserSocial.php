<?php

namespace Squadron\User\Models;

use Squadron\Base\Models\BaseModel;
use Squadron\User\Models\Traits\ConnectedWithUser;

/**
 * Class UserSocial.
 *
 * @property string $userUuid
 * @property string $provider
 * @property string $providerId
 */
class UserSocial extends BaseModel
{
    use ConnectedWithUser;

    protected $fillable = ['userUuid', 'provider', 'providerId'];
}
