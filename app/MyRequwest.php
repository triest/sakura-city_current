<?php

namespace App;


use Illuminate\Database\Eloquent\Model;



class MyRequwest extends Model
{

    protected $table = 'requwest';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'who_id',
        'target_id'
    ];

    public function who()
    {
        return $this->hasMany('App\User');
    }

    public function target()
    {
        return $this->hasMany('App\User');
    }

    public function fromContact()
    {
        return $this->hasOne(User::class, 'id', 'who_id');
    }

}