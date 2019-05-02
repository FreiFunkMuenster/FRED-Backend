<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Userlog extends Model
{
    //
    protected $fillable = [
        'id',
        'tag',
        'level',
        'message',
        'time',
        'app_user_id'
    ];

}
