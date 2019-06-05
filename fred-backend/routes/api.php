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

Route::get('', function (Request $request) {
    return ["status" => "F.R.E.D API up and running."];
});


Route::group(['middleware' => ['api-key']], function () {
    Route::post('v1/app-user/create', "ApiAppUserController@create");
    Route::post('v1/app-user/update', "ApiAppUserController@update");
    Route::get('v1/scans/get/byRectangle', "ApiScanController@getByRectangle");
});


Route::group(['middleware' => ['app-user', 'api-key']], function () {
    Route::post('v1/scans/create', "ApiScanController@create");
    Route::post('v1/logs/create', "UserLogsController@create");
});


/*  TODO: REMOVE THIS SECTION ON A PRODUCTIVE SYSTEM */
Route::get('v1/test', "ApiScanController@getLatestScans");
Route::get('v1/logs', "UserLogsController@getLogView");


Route::get('v1/networks/', "ApiNetworkController@getAll");