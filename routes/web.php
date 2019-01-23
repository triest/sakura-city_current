<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Input;
use App\Region;
use App\City;
use App\Girl;
use App\User;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/welcome-test', function () {
    return view('welcome-test');
});

Route::get('/anket', 'GirlsController@index')->name('main');
Route::get('/anket/{id}', 'GirlsController@showGirl')->name('showGirl');


//пользователь соглашение
Route::get('/Terms', 'GirlsController@showTerms')->name('showTerms')->middleware('auth');;
Route::get('/createAnketPage', 'AnketController@createGirl')->name('createGirlPage')->middleware('auth');;
Route::post('/anket/create', 'AnketController@Store')->name('girlsCreate');


Route::post('/yandex', 'GirlsController@reciverYandex')->name('yandexPost');

//форма таетирования
Route::get('/testpost', function () {
    return view('testPost');
});


//аутентииакация
Route::get('auth/login', 'Auth\AuthController@getLogin')->name('autorization');
Route::post('auth/login', 'Auth\AuthController@postLogin');


Route::get('auth/logout', 'Auth\AuthController@logout')->name('logout')->middleware('auth');;
Route::get('/user/anketa', 'AnketController@girlsShowAuchAnket')->name('girlsShowAuchAnket');

// Маршруты регистрации...
Route::get('auth/register', 'Auth\RegisterController@getRegister')->name('registration');
Route::post('auth/register', 'Auth\AuthController@postRegister');
Auth::routes();

//Восстановление пароля по SMS, страница
Route::get('password/resetsms', function () {
    return view('resetSMS');
})->name('resetsms');
//Отправить сис
//

Route::get('/sendSMS', function () {
    $phone = Input::get('phone');
    // dump($phone);
    $user = collect(DB::select('select * from users where phone like ?', [$phone]))->first();
    //   dump($user);
    if ($user == null) {
        echo 'user not found';
        return Response::json($user);
    }

    //если найден,то
    //1)генерируем проль для отправки

    $activeCode = rand(1000, 9999);
    //  $this->sendSMS($user->phone,$activeCode);
    // $activeCode=1111;
    //2) отправляем его в смс
    App::call('App\Http\Controllers\GirlsController@sendSMS', [$user->phone, $activeCode]);

    //теперь запишем его в БД
    DB::update('update users set smsResetCode = ? where id = ?', [$activeCode, $user->id]);


    return Response::json(['result' => 'ok']);
}
);

Route::get('/sendSMS2', function () {
    $phone = Input::get('phone');
    // dump($phone);
    $user = collect(DB::select('select * from users where phone like ?', [$phone]))->first();
    //   dump($user);
    if ($user != null) {
        echo 'Phone alredy exist!';
        return Response::json($user);
    }

    $user = Auth::user();
    //если найден,то
    //1)генерируем проль для отправки
    $user->phone = $phone;
    $activeCode = rand(1000, 9999);
    $user->actice_code = $activeCode;
    $user->save();

    //2) отправляем его в смс
    App::call('App\Http\Controllers\GirlsController@sendSMS', [$phone, $activeCode]);

    return Response::json(['result' => 'ok']);
}
);

Route::get('/sendCODE2', function () {
    $code = Input::get('code');

    $user = Auth::user();
    if ($user->phone_conferd == 1) {
        return Response::json(['result' => 'alredy']);
    }
    if ($code == $user->actice_code) {
        $user->phone_conferd = 1;
        $user->save();
        return Response::json(['answer' => 'ok']);
    } else {
        return Response::json(['result' => 'fail']);
    }
}
);


//
Route::get('/sendCODE', function () {
    $code = Input::get('code');
    $phone = Input::get('phone');
    $user = User::select(['id', 'name', 'smsResetCode', 'email'])
        ->where('phone', '=', $phone)
        ->first();
    if ($code == $user->smsResetCode) {
        $token = str_random(16);
        DB::table('users')->where('id', $user->id)->update(['email_token' => $token]);
        return Response::json(['andwer' => 'ok', 'token' => $token]);
    } else {
        return Response::json(['result' => 'fail']);
    }
    return Response::json(['result' => 'ok']);
}
);

//отправка sms сброса
Route::post('/resetPasswordSMS', 'GirlsController@resetPassSMS')->name('ResetPassSMS');


//тут для работы с анкетой за деньги
Route::get('/firtPlase/{id}', 'GirlsController@toFirstPlace')->name('TofirstPlase');
Route::post('/toTop/', 'GirlsController@toTop')->name('ToTop');

Route::get('/testmail', 'GirlsController@testMail');


// для администратора
Route::get('/adminPanel', 'AdminController@getAdminPanel')->name('adminPanel')->middleware('auth');;
Route::post('/SetPriceToFirstPlase/',
    'AdminController@SetPriceToFirstPlase')->name('SetToFirstPlase')->middleware('auth');;
Route::post('/SetPriceToTop/', 'AdminController@SetPriceToTop')->name('SetToTopPrice')->middleware('auth');;


//почта
Route::get('/testmail', 'GirlsController@testemail');


Route::get('/confirnEmail', 'GirlsController@MailtoConfurn')->name('sendConfurmEmail');

Route::get('/user/confernd/{token}', 'GirlsController@conferndEmail')->name('conferndEmail');

//смс
Route::get('/sms', 'GirlsController@sendSmsTest');

//ввод номера телефона
Route::get('/inputphone', function () {
    return view('inputphone');
})->name('inputMobile');

Route::post('/inputPhone', 'GirlsController@inputPhone')->name('inputMobilePhone');
Route::post('/inputCode', 'GirlsController@inputActiveCode')->name('inputActiveCode');

//тут путь для правил
Route::get('/rules', function () {
    return view('rules');
})->name('rules')->middleware('auth');;
Route::post('/rules2', 'GirlsController@akceptRules')->name('aceptRules');


Route::get('/user/anketa/edit/', 'AnketController@girlsEditAuchAnket')->name('girlsEditAuchAnket')->middleware('auth');;
Route::post('/user/anketa/edit/', 'AnketController@edit')->name('girlsEdit');

//бот
Route::get('/bot', 'GirlsController@bot')->name('bot');

//галерея
Route::get('/galeray', 'AnketController@galarayView')->name('galeray')->middleware('auth');;
Route::get('/image/delete/{imege}', 'AnketController@deleteimage')->name('deleteImage')->middleware('auth');;
Route::post('/image/upload', 'AnketController@uploadimage')->name('uploadImage')->middleware('auth');;
Route::post('/image/main/upload', 'GirlsController@uploadMainimage')->name('uploadMainImage')->middleware('auth');;

Route::get('/message', 'MessagesController@GetMessagesPage')->middleware('auth');;

Route::get('/cityes', 'GirlsController@getSearch');

//for dropdown
Route::get('/prodview', 'TestController@prodfunct');
Route::get('/findProductName', 'TestController@findProductName');
Route::get('/findPrice', 'TestController@findPrice');

//Route::get('/findRegions/{id}','GirlsController@findRegions');
Route::get('/findRegions', function () {
    $id = Input::get('country_id');
    $regions = Region::where('id_country', '=', $id)
        ->orderBy('id')
        ->get();
    //dump($regions);

    return Response::json($regions);
}
);

Route::get('/findCitys', function () {
    $id = Input::get('region_id');

    /*  $city=collect(DB::select('select * from city where id_region=?',
      [$id]));*/
    $city = City::where('id_region', '=', $id)
        ->orderBy('id')
        ->get();

    return Response::json($city);
});

Route::get('/findCitys2', function () {
    $id = Input::get('region_id');
    //  dump($id);
    $region = collect(DB::select('select * from regions where id_region=?',
        [$id]));
    //   dump($region);
    $city = collect(DB::select('SELECT * FROM `cities` WHERE `id_region`=? ',
        [$id]));
    //   dump($city);
    return Response::json($city);
});

Route::get('/inputPhone2', function () {
    $serveses = null;
    //   echo 'test';
    $user = Auth::user();
    if (Auth::guest()) {
        return redirect('/login');
    }
    $girl = Girl::select([
        'name',
        'email',
        'password',
        'id',
        'phone',
        'description',
        'enabled',
        'payday',
        'payed',
        'login',
        'main_image',
        'sex',
        'meet',
        'weight',
        'height',
        'age'
    ])
        ->where('user_id', $user->id)->first();
    if ($girl != null) {
        $rewest = new Request();
        return $this->girlsShowAuchAnket($rewest);
    };
    if (Auth::guest()) {
        return redirect('/login');
    }
    if ($user->akcept == 0) {
        return view('rules');
    }

    if ($user->is_conferd == 0) {
        return view('conferntEmail')->with(['email' => $user->email]);
    }

    if ($user->phone == null) {
        return view('inputphone2');
    }
    if ($user->phone_conferd == 0) {
        return view('inputphone2');
    }
    if ($girl != null) {
        return $this->index();
    }
    $phone = $user->phone;
    $countries = collect(DB::select('select * from countries')); //получаем города
    //получили цену
    $title = "Создание анкеты";
    return view('createGirl')->with([
            'servises' => $serveses,
            'title' => $title,
            'phone' => $phone,
            'countries' => $countries
        ]
    )->name('inputMobilePhone2');

});

Route::get('/inpotMobilePhoneAjax', function () {
    $phone = Input::get('phone');
    $ansver = "test";
    return Response::json($ansver);
});;

//тестирование верстки
Route::get('/bladetest', function () {
    return view('bladetest');
})->name('bladetest');

//поиск анкет
Route::post('/search', 'GirlsController@search')->name('search');
Route::get('/reset', 'GirlsController@index2')->name('reset');

//действия адмиистратора с анкетой
Route::post('/admin-to-girl/', 'MessagesController@adminToGirl')->name('adminToGirl');
Route::post('/gitl-to-admin/', 'MessagesController@girlToAdmin')->name('girlToAdmin');


//сообщения
Route::get('/messages', 'MessagesController@getMessagePage')->name('MessagePage')->middleware('auth');;
Route::get('/messages/{girl_id}',
    'MessagesController@getMessagePageAdmin')->name('MessagePageAdmin')->middleware('auth');;

Route::get('/usersList', 'AdminController@usersList')->name('usersList')->middleware('auth');;
Route::get('/messageList', 'MessagesController@messagesList')->name('messageList')->middleware('auth');;
Route::get('/moneyHistory', 'AdminController@moneyHistory')->name('moneyHistory')->middleware('auth');;

//custom user
//Route::get('/custom', 'CustomUserController@index')->name('custom');
Route::get('/join/', 'CustomUserController@index')->name('join');
Route::post('/join/new', 'CustomUserController@join')->name('joinStore');
Route::get('/continion/', 'AnketController@createGirl')->name('continion');

//приватный доступ
//makePrivateRequwest
Route::get('/privateRequwest/{id}', 'PrivateController@makeRequwest')->name('makePrivateRequwest')->middleware('auth');;

//просмотр отправденныъ запросов
Route::get('/requwestForMe', 'PrivateController@requwestForMe')->name('requwestForMe')->middleware('auth');;

//предоставить доступ
Route::get('/makeAccess/{id}', 'PrivateController@makeAccess')->name('makeAccess')->middleware('auth');;

Route::get('/denadeAccess/{id}', 'PrivateController@denideAccess')->name('denideAccess')->middleware('auth');;