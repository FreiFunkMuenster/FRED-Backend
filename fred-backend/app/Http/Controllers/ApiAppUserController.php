<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiAppUserController extends Controller
{
    public function create(Request $request){
        return \App\AppUser::create(
            [
                "hash" => \Illuminate\Support\Str::random(64),
                "device_make" => $request->input("device-make", null),
                "device_model" => $request->input("device-model", null)
            ]
        );
    }
    public function update(Request $request){

        $appUser = \App\AppUser::where('hash', $request->hash)->first();

        $appUser->nickname = $request->nickname;
        $appUser->save();
        return json_encode("true");
    }
}
