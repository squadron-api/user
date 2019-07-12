<?php

namespace Squadron\User\Http\Controllers\Api;

use Laravel\Socialite\Facades\Socialite;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Squadron\Base\Helpers\ApiResponse;
use Squadron\Base\Http\Controllers\BaseController;
use Squadron\CRUD\Http\Controllers\Traits\ProvidesCRUD;
use Squadron\User\Events\TokenCreated;
use Squadron\User\Http\Requests\UserRequest;
use Squadron\User\Models\User;
use Squadron\User\Models\UserSocial;

class UserController extends BaseController
{
    use ProvidesCRUD;

    public function register(UserRequest $request): JsonResponse
    {
        $data = $request->all();
        $password = ! empty($data['socialProvider'])
                        ? Str::random(config('squadron.user.randomPasswordLength', 16))
                        : $data['password'];

        // create user
        $newUser = new User();
        $newUser->fill($data);

        $newUser->email = $data['email'];
        $newUser->password = $password;

        if ($newUser->save())
        {
            // attach social
            if (! empty($data['socialProvider']) && ! empty($data['socialAccessToken']))
            {
                $provider = $data['socialProvider'];
                $accessToken = $data['socialAccessToken'];

                $socialUser = Socialite::driver($provider)->userFromToken($accessToken);

                if ($socialUser !== null && ! empty($socialUser->id))
                {
                    try
                    {
                        UserSocial::create([
                            'userUuid' => $newUser->uuid,
                            'provider' => $provider,
                            'providerId' => $socialUser->id,
                        ]);
                    }
                    catch (\Exception $e)
                    {
                        return ApiResponse::error('Social user already attached');
                    }
                }
            }

            $data = ['access_token' => $newUser->createToken('Post-registration token')->accessToken];

            $tokenCreatedEvent = new TokenCreated($newUser, $data);
            event($tokenCreatedEvent);

            return ApiResponse::success('Registration successful', $tokenCreatedEvent->tokenData);
        }

        return ApiResponse::error('Registration failed');
    }

    public function update(User $user, UserRequest $request)
    {
        $data = $request->post();

        return $this->apiCrud->update(
            $user, $data,

            function ($updateUser) use ($data) {
                // set new password
                if (array_key_exists('password', $data))
                {
                    $updateUser->password = $data['password'];
                }
            }
        );
    }

    public function current(Request $request)
    {
        return $this->apiCrud->getSingle($request->user());
    }

    public function updateCurrent(UserRequest $request)
    {
        return $this->update($request->user(), $request);
    }
}
