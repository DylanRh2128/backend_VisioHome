<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken as Middleware;
use Illuminate\Support\Facades\Log;

class CustomVerifyCsrfToken extends Middleware
{
    /**
     * Determine if the session and input CSRF tokens match.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function tokensMatch($request)
    {
        Log::info('CSRF DEBUG VERIFY', [
            'session_token' => $request->session()->token(),
            'input_token' => $request->input('_token'),
            'header_token' => $request->header('X-XSRF-TOKEN'),
            'cookie_token' => $request->cookie('XSRF-TOKEN'),
        ]);

        return parent::tokensMatch($request);
    }
}
