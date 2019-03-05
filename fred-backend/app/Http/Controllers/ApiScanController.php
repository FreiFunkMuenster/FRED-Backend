<?php

namespace App\Http\Controllers;

use App\Scan;
use Illuminate\Http\Request;

class ApiScanController extends Controller
{
    public function create(Request $request)
    {

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


    }


    public function getByRectangle(Request $request)
    {

        if ($request->bottomleft_longitude && $request->bottomleft_latitude && $request->topright_latitude && $request->topright_longitude) {

            $result = Scan::where([
                ['latitude', '>=', $request->bottomleft_latitude],
                ['longitude', '>=', $request->bottomleft_longitude],
                ['latitude', '<=', $request->topright_latitude],
                ['longitude', '<=', $request->topright_longitude]
            ])->get();
            return json_encode($result);
        } else {

            return json_encode(["Error" => "One or multiple of the following parameter are missed: bottomleft_longitude, bottomleft_latitude, topright_longitude, topright_latitude"]);

        }


    }

    public function getLatestScans(Request $request)
    {
        $result = [];
        $result[] = "Warning: This function will be removed on productive system.";
        foreach (\App\Scan::orderBy('id', 'desc')->limit(5)->get() as $scan) {
            $scan->scanData;  // load scan data once
            $result[] = $scan;
        }
        return $result;
    }
}
