<?php

namespace App\Http\Middleware;

use App\AppUser;
use Closure;
use Illuminate\Http\Response;

class CheckUserHash
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

            $appUser = AppUser::where('hash',$request->hash)->first();

            if(!$appUser)
                return Response::create(
                    json_encode(['return_code' => 'ERROR', 'error_code' => 'WRONG_USER_HASH' ,'error_text'=>'Please provide a correct user hash.']),
                    403);
            else
                return $next($request);
    }
}
