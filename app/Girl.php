<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Comment;
use App\Message;
use Auth;
use DB;

class Girl extends Model
{
    //
    protected $fillable = [
        'name',
        'email',
        'password',
        'id',
        'phone',
        'description',
        'private',
        'enabled',
        'payday',
        'payed',
        'login',
        'main_image',
        'publicated',
        'money',
        'beginvip',
        'endvip',
        'sex',
        'meet',
        'weight',
        'height',
        'country_id',
        'region_id',
        'city_id',
        'banned',
        'created_at'
    ];


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

    public function getVip()
    {
        $current_date = Carbon::now();
        $vipGirls = Girl::select(['id', 'name', 'login', 'email', 'phone', 'main_image', 'description'])
            ->where('beginvip', '<', $current_date)
            ->where('endvip', '>', $current_date)
            ->orderBy('created_at', 'DESC')
            ->orderBy('rating', 'ASC');
        return $vipGirls;
    }

    //получаем сообщения
    public function getMessages()
    {
        if (Auth::check()) {
            $user = Auth::user();  // и если админ
            $user_id = $user->id;
        } else {
            return null;
        }
        //  dump($user_id);
        $girl = Girl::select(['id', 'name', 'main_image', 'banned'])->where('user_id', $user_id)->first();

        if ($user->isAdmin == 0) {
            if ($girl != null) {
                $messages = Message::select('id', 'text', 'girl_id', 'fromAdmin', 'date', 'readed')
                    ->where('girl_id', $girl->id)
                    ->where('fromAdmin', '=', 1)
                    ->where('readed', '=', 0)
                    ->get();
                // dump($messages);
                if (empty($messages)) {
                    return null;
                }
                return $messages;
            }
        } else {
            if ($girl != null) {
                $messages = Message::select('id', 'text', 'girl_id', 'fromAdmin', 'date', 'readed')
                    ->where('girl_id', $girl->id)
                    ->where('fromAdmin', '=', 0)
                    ->where('readed', '=', 0)
                    ->get();
                //  dump($messages);

                return $messages;
            }
            return null;
        }
    }

    function isTarget($user_id)
    {
        $user = Auth::user();
        //выбирем в запросах, есть ли запрос от авторизованного user для данной анкеты
        $rez = DB::select('select * from requwest where who_id=? and  target_id=?', [$user->id, $user_id]);
        if ($rez == null) {
            return null;
        } else {
            $rez = $rez[0];
            return $rez;
        }
    }

    //возвращает, есть ли запросы на добавление на открытие приватной инфомации в анкете
    function hasRequwest()
    {
        $user = Auth::user();
        $rez = DB::select('select * from requwest where  target_id=? and rezult=\'not_dispersed\'', [$user->id]);
        if ($rez == null) {
            return null;
        } else {
            $rez = $rez[0];
            return $rez;
        }
    }
}
