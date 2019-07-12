<?php

namespace Squadron\User\Http\Controllers\Traits;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Passport\Token;
use Squadron\Base\Helpers\ApiResponse;
use Squadron\User\Events\TokenCreated;
use Squadron\User\Models\User;

trait ResetsPasswords
{
    use \Illuminate\Foundation\Auth\ResetsPasswords;

    private $tokenData;

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules(): array
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => array_merge(
                ['required', 'confirmed'],
                config('squadron.user.passwordRules', [])
            ),
        ];
    }

    /**
     * Reset the given user's password.
     *
     * @param User   $user
     * @param string $password
     */
    protected function resetPassword(User $user, $password): void
    {
        $user->password = $password;

        $user->setRememberToken(Str::random(60));
        $user->save();

        event(new PasswordReset($user));

        // revoke all previous access tokens
        /** @var Token $accessToken */
        foreach ($user->tokens as $accessToken)
        {
            $accessToken->revoke();
        }

        // generating new access token
        $data = ['access_token' => $user->createToken('Restore password token')->accessToken];

        $tokenCreatedEvent = new TokenCreated($user, $data);
        event($tokenCreatedEvent);

        $this->tokenData = $tokenCreatedEvent->tokenData;
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param Request $request
     * @param string  $message
     *
     * @return JsonResponse
     */
    protected function sendResetResponse(Request $request, string $message): JsonResponse
    {
        return ApiResponse::success(trans($message), $this->tokenData);
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param Request $request
     * @param string  $message
     *
     * @return JsonResponse
     */
    protected function sendResetFailedResponse(Request $request, string $message): JsonResponse
    {
        return ApiResponse::error(trans($message));
    }
}
