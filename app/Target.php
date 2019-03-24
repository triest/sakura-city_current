<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    //
    protected $table = "target";

    public function user()
    {
        return $this->hasMany('App\Girl');
    }

    public function girl()
    {
        return $this->belongsToMany('App\Girl', 'girl_target');
    }

}
