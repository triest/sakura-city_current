<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    //
    protected $fillable = [
        'id',
        'text',
        'girl_id',
        'fromAdmin',
        'date',
        'readed',
        'adminreaded'
    ];


    public function who()
    {
        return $this->belongsTo('App\User');
    }
}
