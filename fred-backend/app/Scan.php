<?php
/**
 * Created by PhpStorm.
 * User: adeltfl
 * Date: 18.01.2019
 * Time: 15:11
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Scan extends Model
{
    protected $fillable = ["longitude","latitude","app_user_id"];

    public function scanData() {
        return $this->hasMany(NetworkScanDataSet::class, 'scan_id', 'id');
    }

    public function AppUser() {
        return $this->hasOne(AppUser::class, 'id', 'app_user_id');
    }


}