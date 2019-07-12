<?php

namespace Squadron\User\Http\Resources;

use Squadron\Base\Http\Resources\BaseResource;

/**
 * Class UserResource.
 *
 * @mixin \Squadron\User\Models\User
 */
class UserResource extends BaseResource
{
    /**
     * {@inheritdoc}
     */
    public function toArray($request): array
    {
        return [
            'uuid' => $this->uuid,
            'active' => $this->active,
            'email' => $this->email,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'role' => $this->role,
        ];
    }
}
