<?php

namespace Squadron\User\Http\Controllers\Api;

use Squadron\Base\Http\Controllers\BaseController;
use Squadron\User\Http\Controllers\Traits\ResetsPasswords;

class ResetPasswordController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    public function __construct()
    {
        parent::__construct();

        $this->middleware('guest');
    }
}
