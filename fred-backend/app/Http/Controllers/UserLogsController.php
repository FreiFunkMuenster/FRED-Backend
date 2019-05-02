<?php

namespace App\Http\Controllers;

use App\Scan;
use App\Userlog;
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
                    'time' => $log['time'],
                    'level' => $log['level']
                ]);
                $cntSucess++;
            } catch (\Exception $e) {
                $cntErrors++;
            }

        }
        return ["created_logs" => $cntSucess, "failed_logs" => $cntErrors];


    }

    // todo: subject to remove on production
    public function getLogsView(Request $request) {
        $order = $request->get('order', 'desc');

        $logs = Userlog::all()->orderBy('time', $order)->paginate(150);

        view('logs', [
            'logs' => $logs
        ]);
    }

}
