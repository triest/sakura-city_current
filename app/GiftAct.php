<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GiftAct extends Model
{
    //
    protected $table = "gift_act";


    protected $fillable = ['id', 'present_id', 'who_id', 'target_id','readed'];

    public function who()
    {
        return $this->hasOne('App\User');
    }

    public function target()
    {
        return $this->hasOne(User::class, 'id', 'target_id');
    }

    public function gift()
    {
        return $this->hasOne('App\Present');
    }

    public function fromContact()
    {
        return $this->hasOne(User::class, 'id', 'who_id');
    }


}
