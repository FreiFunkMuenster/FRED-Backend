<?php

namespace App\Console\Commands;

use App\Network;
use Illuminate\Console\Command;

class CalculateNetworkPositions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Networks:CalculatePositions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $time_pre = microtime(true);
        $networks = Network::all();
        $amount_networks = count($networks);
        $current_counter = 0;
        $this->info("\nStarting recalculation of network positions.\n" );
        $sum_datapoints = 0;
        foreach ($networks as $network){
            $current_counter++;

            $sum_longitude = 0;
            $sum_latitude = 0;
            $datapoints = 0;
            foreach ($network->getNetworkScanData()->get() as $scanData){
                $datapoints++;
                $sum_longitude += $scanData->scan()->first()->longitude;
                $sum_latitude += $scanData->scan()->first()->latitude;

            }
            if($datapoints>0){
                $sum_datapoints += $datapoints;
                $network->calculated_longitude = $sum_longitude / $datapoints;
                $network->calculated_latitude = $sum_latitude / $datapoints;
                $network->datapoints = $datapoints;
                $network->save();
            }

            $this->line(round($current_counter/$amount_networks*100 )."% | Calculated network $network->bssid | Datapoints: $datapoints");
        }

        $time_post = microtime(true);
        $exec_time = round($time_post - $time_pre,3);

        $this->info("\nDone. Execution time: ".$exec_time."s | Calculated Networks: ".count($networks)." | Datapoints: $sum_datapoints");
            }
}
