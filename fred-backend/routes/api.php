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

        $cntSucessScan = 0;
        $cntSucessNetwork = 0;
        $cntErrorsNetwork = 0;
        $cntErrorsScan = 0;
        foreach ($request->scans as $scan) {
            if (isset($scan["latitude"]) && isset($scan["longitude"]) && is_numeric($scan["latitude"]) && is_numeric($scan["longitude"])) {

                $newScan = \App\Scan::create([
                    "longitude" => $scan["longitude"],
                    "latitude" => $scan["latitude"],
                    "app_user_id" => $appUser->id
                ]);


                foreach ($scan['networks'] as $scannedNetwork) {
                    if (isset($scannedNetwork['bssid']) && isset($scannedNetwork['ssid'])) {
                        $network = \App\Network::where("bssid", $scannedNetwork['bssid'])->first();
                        if (!$network) {

                            $network = \App\Network::create(["bssid" => $scannedNetwork['bssid']]);

                        }

                        $scannedNetwork = array_merge(["network_id" => $network->id, "scan_id" => $newScan->id], $scannedNetwork);


                        \App\NetworkScanDataSet::create($scannedNetwork);


                        $cntSucessNetwork++;
                    } else {
                        $cntErrorsNetwork++;
                    }
                }
                $cntSucessScan++;
            } else {
                $cntErrorsScan++;
            }

        }
        return ["created_scans" => $cntSucessScan, "failed_scans" => $cntErrorsScan, "created_networks" => $cntSucessNetwork, "failed_networks" => $cntErrorsNetwork];

    });


});
