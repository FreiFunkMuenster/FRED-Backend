<?php
/**
 * Created by PhpStorm.
 * User: adeltfl
 * Date: 18.01.2019
 * Time: 14:58
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Network extends Model
{
    protected $fillable = ['bssid'];


    public function getNetworkScanData() {
        return $this->hasMany(NetworkScanDataSet::class, 'network_id', 'id');
    }
}