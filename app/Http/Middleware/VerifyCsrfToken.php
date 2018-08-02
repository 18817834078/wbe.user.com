<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'http://wbe.user.com/api/login',
        'http://wbe.user.com/api/join',
        'http://wbe.user.com/api/change_pw',
        'http://wbe.user.com/api/add_address',
        'http://wbe.user.com/api/edit_address',
        'http://wbe.user.com/api/add_cart',
        'http://wbe.user.com/api/forget_password',
        'http://wbe.user.com/api/add_order',
    ];
}
