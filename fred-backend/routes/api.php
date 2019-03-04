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

Route::get('', function (Request $request) {
    return ["status" => "F.R.E.D API up and running."];
});

Route::get('v1/test', function (Request $request) {
    $result = [];

    foreach (\App\Scan::orderBy('id', 'desc')->limit(5)->get() as $scan) {
        $scan->scanData;  // load scan data once
        $result[] = $scan;
    }

    return $result;
});

Route::group(['middleware' => ['api-key']], function () {
    Route::post('v1/app-user/create', "ApiAppUserController@create");
});


Route::group(['middleware' => ['app-user', 'api-key']], function () {
    Route::post('v1/scans/create', "ApiScanController@create");
});
