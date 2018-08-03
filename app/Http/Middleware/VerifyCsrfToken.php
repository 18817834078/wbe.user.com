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
        'api/login',
        'api/join',
        'api/change_pw',
        'api/add_address',
        'api/edit_address',
        'api/add_cart',
        'api/forget_password',
        'api/add_order',
    ];
}
