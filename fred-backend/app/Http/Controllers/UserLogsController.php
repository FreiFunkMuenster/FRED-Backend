<?php

namespace App\Http\Controllers;

use App\Scan;
use App\Userlog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserLogsController extends Controller
{
    public function create(Request $request)
    {
        $appUser = \App\AppUser::where('hash', $request->hash)->first();

        $cntSucess = 0;
        $cntErrors = 0;
        foreach ($request->logs as $log) {

            try {
                Userlog::create([
                    'tag' => $log['tag'],
                    'message' => $log['message'],
                    'app_user_id' => $appUser->id,
                    'time' => Carbon::createFromTimestamp(intval($log['time'])),
                    'level' => $log['level']
                ]);
                $cntSucess++;
            } catch (\Exception $e) {
                $cntErrors++;
              print_r($e->getMessage());
            }

        }
        return ["created_logs" => $cntSucess, "failed_logs" => $cntErrors];


    }

    // todo: subject to remove on production
    public function getLogView(Request $request) {
        $order = $request->get('order', 'desc');

        $logs = Userlog::orderBy('time', $order)->paginate(150);

        return view('logs', [
            'logs' => $logs
        ]);
    }

}
