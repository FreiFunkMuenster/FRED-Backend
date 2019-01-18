<?php
/**
 * Created by PhpStorm.
 * User: adeltfl
 * Date: 18.01.2019
 * Time: 15:15
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class AppUser extends Model
{
    protected $fillable = ['nickname','hash','device_make','device_model'];
}