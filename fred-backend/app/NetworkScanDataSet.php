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
    protected $fillable = ['network_id','scan_id','ssid', 'capabilities', 'frequency', 'level', 'distance', 'distance-sd', 'passpoint', 'channel-bandwidth', 'center-frequence-o', 'center-frequence-1', 'mc-responder', 'channel-mode','bss-load-element'];
}