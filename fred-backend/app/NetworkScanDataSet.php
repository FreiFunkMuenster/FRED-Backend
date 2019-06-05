<?php
/**
 * Created by PhpStorm.
 * User: adeltfl
 * Date: 18.01.2019
 * Time: 15:12
 */

namespace App;


use Illuminate\Database\Eloquent\Model;


class NetworkScanDataSet extends Model
{
    protected $fillable = [
        'network_id',
        'scan_id',
        'ssid',
        'capabilities',
        'frequency',
        'level',
        'distance',
        'distanceSd',
        'passpoint',
        'channelBandwidth',
        'centerFrequency0',
        'centerFrequency1',
        'mcResponder',
        'channelMode',
        'bssLoadElement'
    ];


    public function networkData() {
        return $this->hasOne(Network::class, 'id', 'network_id');
    }

    public function scan() {
        return $this->hasOne(Scan::class, 'id', 'scan_id');
    }
}