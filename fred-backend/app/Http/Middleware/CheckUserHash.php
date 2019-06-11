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

            if(!$appUser) {
                $result = json_encode(['return_code' => 'ERROR', 'error_code' => 'WRONG_USER_HASH' ,'error_text'=>'Please provide a correct user hash.']);
                return Response::create(
                    $result,
                    403,
                    ['Content-Length' => strlen($result)]);
            }
            else
                return $next($request);
    }
}
