<?php

namespace Squadron\User\Http\Requests;

use Illuminate\Validation\Rule;
use Squadron\Base\Http\Requests\BaseRequest;

class SocialAuthRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'socialProvider' => [
                'required',
                Rule::in(config('squadron.user.socialProviders', [])),
            ],
            'socialAccessToken' => 'required|string',
        ];
    }
}
