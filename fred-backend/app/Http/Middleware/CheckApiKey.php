<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class CheckApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $api_key = env("API_KEY", false);

        if (!$api_key) {
            return Response::create(json_encode(['return_code' => 'ERROR', 'error_code' => 'NO_API_KEY_CONFIGURED','error_text'=>'Please set API_KEY in .env configuration file.']));
        }

        if ($request->api_key != $api_key ) {
            return Response::create(json_encode(['return_code' => 'ERROR', 'error_code' => 'INVALID_API_KEY']));
        }

        return $next($request);
    }
}
