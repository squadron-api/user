<?php

namespace Squadron\User\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

use Illuminate\Http\JsonResponse;
use Squadron\Base\Helpers\ApiResponse;
use Squadron\Base\Http\Controllers\BaseController;
use Squadron\User\Events\TokenCreated;
use Squadron\User\Http\Requests\SocialAuthRequest;
use Squadron\User\Models\UserSocial;

class SocialAuthController extends BaseController
{
    public function redirect(string $provider): JsonResponse
    {
        return ApiResponse::success('Use this redirect URI', [
            'redirect' => Socialite::driver($provider)->stateless()->redirect()->getTargetUrl(),
        ]);
    }

    public function redirectBack(string $provider): JsonResponse
    {
        $socialUser = Socialite::driver($provider)->stateless()->user();

        return ApiResponse::success('Social access granted', [
            'socialAccessToken' => $socialUser->token,
        ]);
    }

    public function auth(SocialAuthRequest $request): JsonResponse
    {
        $data = $request->all();

        $provider = $data['socialProvider'];
        $accessToken = $data['socialAccessToken'];

        $socialUser = Socialite::driver($provider)->userFromToken($accessToken);

        if ($socialUser !== null && ! empty($socialUser->id))
        {
            $userModel = config('squadron.user.models.user');
            $userSocialModel = config('squadron.user.models.userSocial');

            $authUser = UserSocial::where([
                ['provider', $provider],
                ['providerId', $socialUser->id],
            ])->first();

            if (! $authUser)
            {
                // no account found - try to auto-attach by email
                $email = $socialUser->email ?? null;

                if ($email !== null)
                {
                    $authUser = $userModel::where('email', $email)->first();

                    if ($authUser)
                    {
                        $userSocialModel::create([
                            'userUuid' => $authUser->uuid,
                            'provider' => $provider,
                            'providerId' => $socialUser->id,
                        ]);
                    }
                }
            }
            else
            {
                $authUser = $authUser->user;
            }

            if ($authUser)
            {
                $data = ['access_token' => $authUser->createToken(sprintf('Social token [%s]', $provider))->accessToken];

                $tokenCreatedEvent = new TokenCreated($authUser, $data);
                event($tokenCreatedEvent);

                return ApiResponse::success('Social auth successful', $tokenCreatedEvent->tokenData);
            }

            return ApiResponse::error('Social account not attached');
        }

        return ApiResponse::error('Invalid accessToken');
    }

    public function attach(SocialAuthRequest $request): JsonResponse
    {
        $data = $request->all();

        $provider = $data['socialProvider'];
        $accessToken = $data['socialAccessToken'];

        $socialUser = Socialite::driver($provider)->userFromToken($accessToken);
        $authUser = Auth::user();

        if ($socialUser !== null && ! empty($socialUser->id))
        {
            try
            {
                $userSocialModel = config('squadron.user.models.userSocial');

                $userSocialModel::create([
                    'userUuid' => $authUser->uuid,
                    'provider' => $provider,
                    'providerId' => $socialUser->id,
                ]);

                return ApiResponse::success('Social account successfully attached');
            }
            catch (\Exception $e)
            {
                return ApiResponse::error('Social user already attached');
            }
        }

        return ApiResponse::error('Invalid accessToken');
    }
}
