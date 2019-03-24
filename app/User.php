<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'money',
        'endvip',
        'beginvip',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    //проверка есть ли анкета
    public function anketisExsis()
    {

        $user_id = Auth::user()->id;
        //  dump($user_id);
        $girl = Girl::select(['id', 'name', 'main_image', 'banned'])->where('user_id', $user_id)->first();

        return $girl;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function finfUserById($id)
    {

        dump($id);
    }
}
