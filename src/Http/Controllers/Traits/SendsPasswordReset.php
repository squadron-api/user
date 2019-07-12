<?php

namespace Squadron\User\Http\Controllers\Traits;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Squadron\Base\Helpers\ApiResponse;

trait SendsPasswordReset
{
    use SendsPasswordResetEmails;

    /**
     * Get the response for a successful password reset link.
     *
     * @param Request $request
     * @param string  $message
     *
     * @return JsonResponse
     */
    protected function sendResetLinkResponse(Request $request, string $message): JsonResponse
    {
        if (config('app.env') === 'testing')
        {
            return ApiResponse::success(trans($message));
        }

        return ApiResponse::success(trans($message));
    }

    /**
     * Get the response for a failed password reset link.
     *
     * @param Request $request
     * @param string  $message
     *
     * @return JsonResponse
     */
    protected function sendResetLinkFailedResponse(Request $request, string $message): JsonResponse
    {
        return ApiResponse::error(trans($message));
    }
}
