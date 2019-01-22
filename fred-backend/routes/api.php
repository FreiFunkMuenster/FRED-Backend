<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['middleware' => ['api-key']], function () {

    Route::post('v1/app-user/create', function (Request $request) {
        return \App\AppUser::create(
            [
                "hash" => \Illuminate\Support\Str::random(64),
                "device_make" => $request->input("device-make", null),
                "device_model" => $request->input("device-model", null)
            ]
        );
    });


});


Route::group(['middleware' => ['app-user', 'api-key']], function () {


    Route::post('v1/scans/create', function (Request $request) {
        $appUser = \App\AppUser::where('hash', $request->hash)->first();
        echo $appUser->id;
        $cntSucess = 0;
        $cntErrors = 0;
        foreach ($request->scans as $scan) {
            if (is_numeric($scan["latitude"]) && is_numeric($scan["latitude"])) {

                \App\Scan::create([
                    "longitude" => $scan["longitude"],
                    "latitude" => $scan["latitude"],
                    "app_user_id" => $appUser->id
                ]);
                $cntSucess ++;
            }else{
                $cntErrors++;
            }

        }
        return ["created_scans" => $cntSucess, "errors" => $cntErrors];

    });


});
