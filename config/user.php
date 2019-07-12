<?php

return [
    'randomPasswordLength' => 12,
    'passwordRules' => ['regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/'],

    'models' => [
        'user' => config('auth.providers.users.model'),
        'userSocial' => \Squadron\User\Models\UserSocial::class,
    ],

    'socialProviders' => ['facebook', 'instagram'],

    'restore' => [
        'emailView' => null,
        'token' => [
            'blocksCount' => 4,
            'blockCharactersCount' => 5,
            'delimiter' => '-',
        ],
    ],

    // additional roles (default roles: root, user)
    'additionalRoles' => [],
    'rolesHierarchy' => [],
];
