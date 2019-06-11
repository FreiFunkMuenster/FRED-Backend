<?php

namespace App\Http\Controllers;

use App\Scan;
use Illuminate\Http\Request;

class ApiNetworkController extends Controller
{

    public function getAll(Request $request)
    {

        $result = [];
       // $result[] = "Warning: This function will be removed on productive system.";
        foreach (\App\Network::orderBy('id', 'desc')->get() as $scan) {
            $scan->scanData;  // load scan data once
            $result[] = $scan;
        }
        return json_encode($result);
    }

    public function getVisible(Request $request)
    {

        $result = [];
       // $result[] = "Warning: This function will be removed on productive system.";
        foreach (\App\Network::where('datapoints', '>', 0)->orderBy('id', 'desc')->get() as $scan) {
            $scan->scanData;  // load scan data once
            $result[] = $scan;
        }
        return json_encode($result);
    }


}
