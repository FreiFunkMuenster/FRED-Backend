<?php

namespace App\Http\Controllers;

use App\Scan;
use Illuminate\Http\Request;

class ApiScanController extends Controller
{
    public function create(Request $request)
    {

        try {
            $appUser = \App\AppUser::where('hash', $request->hash)->first();

            $cntSuccessScan = 0;
            $cntSuccessNetwork = 0;
            $cntErrorsNetwork = 0;
            $cntErrorsScan = 0;
            foreach ($request->scans as $scan) {
                if (isset($scan["latitude"]) && isset($scan["longitude"]) && is_numeric($scan["latitude"]) && is_numeric($scan["longitude"])) {

                    $newScan = \App\Scan::create([
                        "longitude" => $scan["longitude"],
                        "latitude" => $scan["latitude"],
                        "app_user_id" => $appUser->id,
                        "altitude" => isset($scan['altitude']) && is_numeric($scan['altitude']) ? $scan['altitude'] : null,
                        "accuracy" => isset($scan['accuracy']) && is_numeric($scan['accuracy']) ? $scan['accuracy'] : null,
                    ]);


                    foreach ($scan['networks'] as $scannedNetwork) {
                        if (isset($scannedNetwork['bssid']) && isset($scannedNetwork['ssid'])) {
                            $network = \App\Network::where("bssid", $scannedNetwork['bssid'])->first();
                            if (!$network) {

                                $network = \App\Network::create(["bssid" => $scannedNetwork['bssid']]);

                            }

                            if($network->calculated_longitude != 0) {
                                $diff_longitude = $network->calculated_longitude - $scan["longitude"];
                                $diff_longitude = $diff_longitude / ($network->datapoints + 1);
                                $network->calculated_longitude += $diff_longitude;

                                $diff_latitude = $network->calculated_latitude - $scan["latitude"];
                                $diff_latitude = $diff_latitude / ($network->datapoints + 1);
                                $network->calculated_latitude += $diff_latitude;
                            }else{
                                $network->calculated_longitude = $scan["longitude"];
                                $network->calculated_latitude = $scan["latitude"];
                            }
                            $network->last_ssid = $scannedNetwork['ssid'];
                            $network->datapoints++;
                            $network->save();

                            $scannedNetwork = array_merge(["network_id" => $network->id, "scan_id" => $newScan->id], $scannedNetwork);


                            \App\NetworkScanDataSet::create($scannedNetwork);


                            $cntSuccessNetwork++;
                        } else {
                            $cntErrorsNetwork++;
                        }
                    }
                    $cntSuccessScan++;
                } else {
                    $cntErrorsScan++;
                }

            }
            return ["created_scans" => $cntSuccessScan, "failed_scans" => $cntErrorsScan, "created_networks" => $cntSuccessNetwork, "failed_networks" => $cntErrorsNetwork];
        } catch (\Exception $e) {
            file_put_contents('latest_error', $e->getMessage());  // todo: create error logs normally
            return abort(500, 'Error while creating scans: ' . $e->getMessage());
        }


    }


    public function getByRectangle(Request $request)
    {

        if ($request->bottomleft_longitude && $request->bottomleft_latitude && $request->topright_latitude && $request->topright_longitude) {

            $pattern = [
                ['latitude', '>=', $request->bottomleft_latitude],
                ['longitude', '>=', $request->bottomleft_longitude],
                ['latitude', '<=', $request->topright_latitude],
                ['longitude', '<=', $request->topright_longitude]
            ];


            if ($request->start) {
                $pattern[] = ['scans.created_at', '>=', $request->start];
            }

            if ($request->end) {
                $pattern[] = ['scans.created_at', '<=', $request->end];
            }

            if ($request->ssid) {
                $pattern[] = ['ssid', 'LIKE', $request->ssid];
            }
            if ($request->not_ssid) {
                $pattern[] = ['ssid', 'NOT LIKE', $request->not_ssid];
            }
            if ($request->bssid) {
                $pattern[] = ['bssid', 'LIKE', $request->bssid];
            }
            if ($request->not_bssid) {
                $pattern[] = ['bssid', 'NOT LIKE', $request->not_bssid];
            }

            if ($request->device_make) {
                $pattern[] = ['device_make', 'LIKE', $request->device_make];
            }

            if ($request->not_device_make) {
                $pattern[] = ['device_make', 'NOT LIKE', $request->not_device_make];
            }

            if ($request->device_model) {
                $pattern[] = ['device_model', 'LIKE', $request->device_model];
            }

            if ($request->not_device_model) {
                $pattern2[] = ['device_model', 'NOT LIKE', $request->not_device_model];
            }
            if ($request->nickname) {
                $pattern[] = ['nickname', 'LIKE', $request->nickname];
            }
            if ($request->not_nickname) {
                $pattern[] = ['nickname', 'NOT LIKE', $request->not_nickname];
            }


            $scans = Scan::select(
                [
                    "scans.id as scan_id",
                    "scans.id as scan_id",
                    "scans.created_at",
                    "app_users.id as user_id",
                    "level",
                    "frequency",
                    "network_id",
                    "capabilities",
                    "longitude",
                    "latitude",
                    "nickname",
                    "device_make",
                    "device_model",
                    "ssid",
                    "bssid", "distance",
                    "distance_sd",
                    "passpoint",
                    "channel_bandwidth",
                    "center_frequency_0",
                    "center_frequency_1",
                    "mc_responder",
                    "channel_mode",
                    "bss_load_element",
                ])
                ->join('app_users', 'app_users.id', '=', 'scans.app_user_id')
                ->join('network_scan_data_sets', 'network_scan_data_sets.scan_id', '=', 'scans.id')
                ->join('networks', 'network_scan_data_sets.network_id', '=', 'networks.id')
                ->where($pattern)
                ->paginate(1000);


            $result = [];

            foreach ($scans as $scan) {
                if (!isset($result[$scan->scan_id])) {
                    $result[$scan->scan_id] = [
                        //"scan_id" => $scan->scan_id,
                        "created_at" => $scan->created_at,
                        "user" => ["id" => $scan->user_id, "nickname" => $scan->nickname, "device_make" => $scan->device_make, "device_model" => $scan->device_model],
                        "position" => [
                            "longitude" => $scan->longitude,
                            "latitude" => $scan->latitude,
                        ],
                        "networks" => [],

                    ];
                }
                $result[$scan->scan_id]["networks"][] = ["id" => $scan->network_id,
                    "bssid" => $scan->bssid,
                    "ssid" => $scan->ssid,
                    "capabilities" => $scan->capabilities,
                    "frequency" => $scan->frequency,
                    "level" => $scan->level,
                    "distance" => $scan->distance,
                    "distance_sd" => $scan->distance_sd,
                    "passpoint" => $scan->passpoint,
                    "channel_bandwidth" => $scan->channel_bandwidth,
                    "center_frequency_0" => $scan->center_frequency_0,
                    "center_frequency_1" => $scan->center_frequency_1,
                    "mc_responder" => $scan->mc_responder,
                    "channel_mode" => $scan->channel_mode,
                    "bss_load_element" => $scan->bss_load_element,
                ];
            }

            return json_encode(["metadata" => ["count" => $scans->count(),
                "currentPage" => $scans->currentPage(),
                "hasMorePages" => $scans->hasMorePages(),
                "lastPage" => $scans->lastPage(),
                "scansPerPage" => $scans->perPage(),
                "total" => $scans->total(),

            ], "result" => $result]);

        } else {
            return json_encode(["Error" => "One or multiple of the following parameter are missing: bottomleft_longitude, bottomleft_latitude, topright_longitude, topright_latitude"]);
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
