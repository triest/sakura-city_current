<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
//use Doctrine\Common\Cache\Cache;
use Illuminate\Support\Facades\Cache;
use Closure;
use App\Girl;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use App\Message;


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
        return $this->belongsTo('App\User');
    }

    public function target()
    {
        return $this->belongsTo('App\User');
    }

    public function getWho(){
        $id=$this->who_id;
        $user=User::select(['id','name'])->where('id',$id)->first();
      //  $girl=$user->girl()->first();
        $girl = Girl::select(['id', 'name','main_image' ])
            ->where('user_id',$user->id)->first();
        return $girl;
    }
}
