<?php

namespace Squadron\User\Http\Requests;

use Illuminate\Validation\Rule;
use Squadron\CRUD\Http\Requests\CreateUpdateRequest;

class UserRequest extends CreateUpdateRequest
{
    /**
     * {@inheritdoc}
     */
    protected function getCreateRules(): array
    {
        return [
            'firstName' => 'required|string|max:50',
            'lastName' => 'required|string|max:50',
            'email' => 'required|email|max:100',

            'password' => array_merge(
                ['required_without:socialProvider', 'string'],
                config('squadron.user.passwordRules', [])
            ),

            'socialProvider' => [
                'required_without:password',
                Rule::in(config('squadron.user.socialProviders', [])),
            ],
            'socialAccessToken' => 'required_with:socialProvider|string',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getUpdateRules(): array
    {
        return [
            'firstName' => 'sometimes|string|max:50',
            'lastName' => 'sometimes|string|max:50',

            'password' => array_merge(
                ['sometimes', 'string'],
                config('squadron.user.passwordRules', [])
            ),
        ];
    }
}
