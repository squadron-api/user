<?php

namespace Squadron\User\Http\Controllers\Api;

use Squadron\Base\Http\Controllers\BaseController;
use Squadron\User\Http\Controllers\Traits\SendsPasswordReset;

class ForgotPasswordController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordReset;

    public function __construct()
    {
        parent::__construct();

        $this->middleware('guest');
    }
}
