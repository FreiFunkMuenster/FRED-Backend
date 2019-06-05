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
        $this->info("Starting recalculation of network positions." . count($networks));
        foreach ($networks as $network){


            $sum_longitude = 0;
            $sum_latitude = 0;
            $datapoints = 0;
            foreach ($network->getNetworkScanData()->get() as $scanData){
                $datapoints++;
                $sum_longitude += $scanData->scan()->first()->longitude;
                $sum_latitude += $scanData->scan()->first()->latitude;

            }
            $network->calculated_longitude = $sum_longitude / $datapoints;
            $network->calculated_latitude = $sum_latitude / $datapoints;
            $network->datapoints = $datapoints;
            $network->save();
            $this->line("Calculating Network $network->bssid  Datapoints: $datapoints");
        }

        $time_post = microtime(true);
        $exec_time = round($time_post - $time_pre,3);
        $this->info("Done. Execution time: ".$exec_time."s");
    }
}
