<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Girl extends Model
{
    //
    protected $fillable = ['name', 'description', 'sex', 'ptivate'];

    public function photos()
    {
        return $this->hasMany('App\Photo');
    }

    public function privatephotos()
    {
        return $this->hasMany('App\Privatephoto');
    }

    public function user()
    {
        return $this->hasOne('App\User');
    }

    public function target()
    {
        //return $this->hasOne('App\Target');
        return $this->belongsToMany('App\Target','girl_target');
    }

}
