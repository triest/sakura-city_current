<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use File;
use Storage;
use DateTime;
use App\User;
use App\Girl;
use App\Photo;
use DB;

class GirlsController extends Controller
{
    //
    function index()
    {
        if (Auth::check()) {
            $user = Auth::user();  // и если админ
            if ($user->isAdmin == 1) {
                $girls = Girl::select(['id', 'name', 'phone', 'main_image', 'description', 'banned'])
                    ->orderBy('created_at', 'DESC')->simplePaginate(9);
            } else {
                $girls = Girl::select(['id', 'name', 'phone', 'main_image', 'description', 'sex'])
                    ->where('banned', '=', '0')
                    ->orderBy('created_at', 'DESC')
                    ->Paginate(9);
            }
        } else {
            $girls = Girl::select(['id', 'name', 'phone', 'main_image', 'description', 'sex'])
                ->where('banned', '=', '0')
                ->orderBy('created_at', 'DESC')
                ->Paginate(9);
        }
        $user = Auth::user();


        return view('index')->with(['girls' => $girls]);
    }

    public function showGirl($id)
    {
        $girl = Girl::select([
            'name',
            'id',
            'description',
            'main_image',
            'sex',
            'meet',
            'weight',
            'height',
            'age',
            'country_id',
            'region_id',
            'city_id',
            'banned',
            'user_id',
            'status',
        ])->where('id', $id)->first();
        if ($girl == null) {
            return $this->index();
        }
        $images = $girl->photos()->get();

        $AythUser = Auth::user();
        $privatephoto = null;

        $targets = $girl->target()->get();

        //проверяем, что просматривающий пользователь зареген.
        if ($AythUser != null) {
            //  $girl_user_id=$girl->user_id;
            $user3 = DB::table('user_user')
                ->where('my_id', $AythUser->id)
                ->where('other_id', $girl->user_id)->first();
            if ($user3 != null) {
                $girl = Girl::select([
                    'name',
                    'id',
                    'description',
                    'main_image',
                    'sex',
                    'meet',
                    'weight',
                    'height',
                    'age',
                    'country_id',
                    'region_id',
                    'city_id',
                    'banned',
                    'user_id',
                    'private',
                ])->where('id', $id)->first();
                $privatephoto = $girl->privatephotos()->get();
            }
        }

        return view('girlView')->with([
            'girl' => $girl,
            'images' => $images,
            'privatephotos' => $privatephoto,
            'targets' => $targets,
        ]);
    }

    public function inputPhone(Request $request)
    {
        $validatedData = $request->validate([
            'phone' => 'required|numeric|min:11|unique:users',
        ]);

        $phone = $request->phone;
        $user = collect(DB::select('select * from users where phone like ?', [$phone]))->first();
        //   dump($user);
        if ($user != null and $user->phone_conferd == 1) {
            //echo 'Phone alredy exist!';
            return response()->json(['result' => 'alredy']);
        }
        $user = Auth::user();
        //если найден,то
        //1)генерируем проль для отправки
        $user->phone = $phone;
        $activeCode = rand(1000, 9999);
        $user->active_code = $activeCode;
        $user->save();
        //2) отправляем его в смс
        //  App::call('App\Http\Controllers\GirlsController@sendSMS', [$phone, $activeCode]);
        return response()->json(['result' => 'ok']);
    }


    public function inputCode(Request $request)
    {
        $validatedData = $request->validate([
            'code' => 'required|numeric|min:11',
        ]);

        $inputCode = $request->code;
        $user = Auth::user();
        if ($user->active_code == $inputCode) {
            $user->phone_confirmed=1;
            $user->save();
            return response()->json(['result' => 'ok']);
        }
        else{
            return response()->json(['result' => 'wrongCode']);
        }
    }
}
