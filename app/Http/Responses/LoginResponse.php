<?php

namespace App\Http\Responses;

use App\Support\SchoolRoles;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        return $request->wantsJson()
            ? response()->json(['two_factor' => false])
            : redirect()->intended(route(SchoolRoles::dashboardRouteNameFor($request->user()), absolute: false));
    }
}
