<?php

namespace App\Http\Controllers;

//use Illuminate\Validation\Validator;
use App\Photo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
//use Illuminate\Contracts\Validation\Validator;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use App\Comment;
use App\Message;
use phpDocumentor\Reflection\Types\Null_;
use Response;
use Symfony\Component\Filesystem\Exception\IOException;
use function Symfony\Component\VarDumper\Tests\Caster\reflectionParameterFixture;
//use Zend\InputFilter\Input;
use Illuminate\Support\Facades\Input;
use Auth;
use Illuminate\Contracts\Auth\Guard;

use App\Repositories\ImageRepository;
use Carbon\Carbon;
use File;
use Storage;
use DateTime;
use App\User;
use App\Girl;
use App\Services;
use App\DemoMail;
use App\Region;
use App\City;
use App\Country;
use Mail;
use settype;
use Hash;
use App\Mail\Reminder;

//use Uts\HotelBundle\Entity\Country;


class GirlsController extends Controller
{
    function index()
    {
        $girls = Girl::select(['id', 'name', 'login', 'email', 'phone', 'main_image', 'description'])
            ->orderBy('created_at', 'DESC')->simplePaginate(9);
        if (Auth::check()) {
            $user = Auth::user();  // и если админ
            if ($user->isAdmin == 1) {
                $girls = Girl::select(['id', 'name', 'login', 'email', 'phone', 'main_image', 'description', 'banned'])
                    ->orderBy('created_at', 'DESC')->simplePaginate(9);
            } else {
                $girls = Girl::select(['id', 'name', 'login', 'email', 'phone', 'main_image', 'description', 'sex'])
                    ->where('banned', '=', '0')
                    ->orderBy('created_at', 'DESC')
                    ->Paginate(9);
            }
        } else {
            $girls = Girl::select(['id', 'name', 'login', 'email', 'phone', 'main_image', 'description', 'sex'])
                ->where('banned', '=', '0')
                ->orderBy('created_at', 'DESC')
                ->Paginate(9);
        }

        return view('index')->with(['girls' => $girls,/*'vipGirls'=>$vipGirls*/]);
    }

    function index2()
    {
        $girls = Girl::select([
            'id',
            'name',
            'login',
            'email',
            'phone',
            'main_image',
            'description'
        ])->simplePaginate(9);
        $current_date = Carbon::now();
        $girls = Girl::select(['id', 'name', 'login', 'email', 'phone', 'main_image', 'description', 'sex'])
            ->orderBy('created_at', 'DESC')
            ->Paginate(9);
        $countries = collect(DB::select('select * from countries')); //получаем страны

        //получаем регионы
        $regions = collect(DB::select('select * from regions where id_country=1')); //получаем страны

        $cities = collect(DB::select('select * from cities where id_region=1'));


        $vipGirls = Girl::select(['id', 'name', 'login', 'email', 'phone', 'main_image', 'description'])
            ->where('beginvip', '<', $current_date)
            ->where('endvip', '>', $current_date)
            ->orderBy('created_at', 'DESC')
            ->orderBy('rating', 'ASC')
            ->Paginate(9);
        return view('index2')->with([
            'girls' => $girls,
            'vipGirls' => $vipGirls,
            'countries' => $countries,
            'regions' => $regions,
            'cities' => $cities
        ]);
    }

    function bot()
    {
        $girls = Girl::select([
            'id',
            'name',
            'login',
            'email',
            'phone',
            'main_image',
            'description'
        ])->simplePaginate(9);
        //  dump($girls);
        $current_date = Carbon::now();
        //   dump($current_date);

        $girls = Girl::select(['id', 'name', 'phone', 'main_image', 'description', 'sex'])
            //  ->where('vip','=','1')
            ->orderBy('created_at', 'DESC')
            ->orderBy('rating', 'ASC')
            ->Paginate(9);
        $vipGirls = Girl::select(['id', 'name', 'login', 'email', 'phone', 'main_image', 'description'])
            ->where('beginvip', '<', $current_date)
            ->where('endvip', '>', $current_date)
            ->orderBy('created_at', 'DESC')
            ->orderBy('rating', 'ASC')
            ->Paginate(9);
        return $girls;
    }

    public static function getVip()
    {
        $current_date = Carbon::now();
        $vipGirls = Girl::select([
            'id',
            'name',
            'login',
            'email',
            'phone',
            'main_image',
            'description',
            'beginvip',
            'endvip',
            'age'
        ])
            //  ->where('vip','=','1')
            ->where('beginvip', '<', $current_date)
            ->where('endvip', '>', $current_date)
            ->where('banned', '=', 0)
            ->orderBy('created_at', 'DESC')
            ->orderBy('rating', 'ASC')
            ->Paginate(9);
        return $vipGirls;
    }

    public function showGirl($id)
    {

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
            'age',
            'country_id',
            'region_id',
            'city_id',
            'banned'
        ])->where('id', $id)->first();

        if ($girl == null) {
            return $this->index();
        }
        $images = $girl->photos()->get();
        if ($girl['country_id'] != null) {
            $country = Country::select(['name'])->where('id_country', $girl['country_id'])->first();
        } else {
            $country = new Country();
            $country['name'] = "-";
        }

        if ($girl['region_id'] != null) {
            $region = Region::select(['name'])->where('id', $girl['region_id'])->first();
        } else {
            $region = new Region();
            $region['name'] = "-";
        }
        if ($girl['city_id'] != null) {

            $city = City::select(['id', 'name', 'id_country', 'id_region'])->where('id', $girl['city_id'])->first();
            if ($city != null) {
                $id = $city->id_country;
                $country = Country::select('id_country', 'name')
                    ->where('id_country', $id)
                    ->first();

                $region = Region::select('id_region', 'name')
                    ->where('id_region', $city->id_region)
                    ->first();
            }
        } else {
            $city = new City();
            $city['name'] = "-";
        }
        return view('girlView')->with([
            'girl' => $girl,
            'images' => $images,
            'country' => $country,
            'region' => $region,
            'city' => $city
        ]);

    }

    public function showTerms()
    {
        return view('usersTerm');
    }


    public function reciverYandex(Request $request)
    {
        $date = Carbon::now();
        File::append(base_path() . '/public/file.txt', 'data2' . PHP_EOL);
        File::append(base_path() . '/public/file.txt',
            'oprration_id:' . $request['operation_id'] . ',' . 'datetime: ' . $request['datetime'] . ',' . $request['sha1_hash'] . ',' . $request['withdraw_amount'] . ',label:' . $request['label'] . ',' . $date . PHP_EOL);
        $user_email = $request['label'];
        echo 'user email:';
        echo $user_email;
        $user_money = $request['amount'];


        //check netefication
        $operation_id = $request['operation_id'];
        if ($operation_id == 'test-notification') {
            try {
                DB::insert('INSERT INTO `money_history`(`date`, `user_email`, `received`,`operation_id`) VALUES (?,?,?,?)',
                    [$date, $user_email, $user_money, 'test']);
            } catch (IOException $exceptione) {

            }
        }
        $user = collect(DB::select('select * from users where email like ?', [$user_email]))->first(); // работает
        if ($user != null && $user_money != null && $user_money > 0) {
            echo 'check money';
            $user_money_database = $user->money;
            $user_money_database += $user_money;

            $user->money = $user_money_database;
            DB::table('users')->where('email', $user_email)->update(['money' => $user_money_database]);
        }

        // вставляем историю
        try {
            DB::insert('INSERT INTO `money_history`(`date`, `user_email`, `received`,`operation_id`) VALUES (?,?,?,?)',
                [$date, $user_email, $user_money, $operation_id]);
        } catch (IOException $exceptione) {

        }
        return response('OK', 200);
    }


    public function toFirstPlace($id)
    {
        $price = collect(DB::select('select price from servises where name=\'toFirstPlase\' '))->first();
        $girl = collect(DB::select('select * from girls where user_id like ?', [$id]))->first();
        if ($girl == null) {
            return redirect('/girls');
        }
        // настроить списание со счета
        // проверим, достаточно ли денег на счету.
        $have_user = collect(DB::select('select money from users where id like ?', [$id]))->first();
        $have_user = $have_user->money;
        $price = $price->price;
        if ($have_user >= $price) {
            $create_date = $girl->created_at;

            $current_date = Carbon::now();

            DB::table('girls')->where('user_id', $id)->update(['created_at' => $current_date]);
            //теперь списываем деньги
            $new_money = $have_user - $price;
            DB::table('users')->where('id', $id)->update(['money' => $new_money]);
            $requwest = new Request();
            return $this->girlsShowAuchAnket($requwest);
        } else {
            echo 'Недостаточно денег.';
        }

        $requwest = new Request();
        return $this->index();
    }

    public function toTop(Request $request)
    {
        $id = $request['user_id'];
        $price = collect(DB::select('select price from servises where name=\'toTop\' '))->first();
        $girl = collect(DB::select('select * from girls where user_id like ?', [$id]))->first();
        if ($girl == null) {
            return redirect('/girls');
        }

        // настроить списание со счета
        // проверим, достаточно ли денег на счету.
        $have_user = collect(DB::select('select money from users where id like ?', [$id]))->first();
        $user_money = $have_user->money;
        $price = $price->price;
        if ($user_money >= $price) {
            $current_date = Carbon::now();
            // получем дату оканчания vip ытатуса
            $end_vip = $girl->endvip;

            $days = $request->days;
            if ($end_vip == null or $end_vip < $current_date) {
                $end_vip = $current_date;
                $end_vip = $this->addDayswithdate($end_vip, $days);
            } else {
                $end_vip = $this->addDayswithdate($end_vip, $days);
            }
            //теперь списываем деньги
            $new_money = $have_user->money - $price * $days;
            DB::table('users')->where('id', $id)->update(['money' => $new_money]);
            //обновляем анкету
            DB::table('girls')->where('id', $girl->id)->update(['endvip' => $end_vip]);
            DB::table('girls')->where('id', $girl->id)->update(['beginvip' => $current_date]);
            //    die();

            $requwest = new Request();
            return $this->girlsShowAuchAnket($requwest);

        } else {
            echo 'Недостаточно денег.';
            $requwest = new Request();
            return $this->girlsShowAuchAnket($requwest);
        }

        $requwest = new Request();
        return $this->girlsShowAuchAnket($requwest);
    }

    public static function testFunction()
    {
        $helloTest = "GekkiTest";
        return $helloTest;
    }

    public function testMail()
    {
        $testname = 'testname1';
        $mail = 'triest21@gmail.com';
        try {
            Mail::send('email.test', ['name' => $testname], function ($message) use ($mail) {
                $message
                    ->to($mail, 'some guy')
                    //->from('newmail.sm@yandex.ru')
                    ->from('sakura-testmail@sakura-city.info')
                    ->subject('Welcome');
                echo 'ok';
            });
        } catch (\Exception $exception) {
            echo '<br>';
            echo 'error:';
            echo '<br>';
            echo $exception->getMessage();
        }
    }

    function addDayswithdate($date, $days)
    {
        $date = strtotime("+" . $days . " days", strtotime($date));
        return date("Y-m-d H:i:s", $date);
    }

    public function getAdminPanel()
    {
        $user = Auth::user();
        if ($user->isAdmin == 1) {
            // теперь надо получить цену

            $priceFirstPlase = collect(DB::select('select price from servises where name=\'toFirstPlase\' '))->first();
            $priceTop = collect(DB::select('select price from servises where name=\'toTop\' '))->first();
            return view('AdminPanel')->with(['priceFirstPlase' => $priceFirstPlase, 'priceToTop' => $priceTop]);;

        } else {
            return redirect('/girls');
        }

        return redirect('/girls');
    }

    //устанавливаем цену за первое место:
    function SetPriceToFirstPlase(Request $request)
    {
        $price = $request['price'];
        $validator = Validator::make($request->all(), [
            'price' => 'required|min:0',
        ]);

        if ($validator->fails()) {
            return redirect('/girls');
        }

        if ($price <= 0) {
            return redirect('/girls');
        }
        //      DB::update('update servises set price = ? where name like \'toTop\'',$price );
        DB::update('update servises set price = ? where name = \'toFirstPlase\'', [$price]);
        return $this->getAdminPanel();

    }

    //устанавливаем цену за шапку:
    function SetPriceToTop(Request $request)
    {
        $price = $request['price'];
        $validator = Validator::make($request->all(), [
            'price' => 'required|min:0',
        ]);

        if ($validator->fails()) {
            return redirect('/girls');
        }

        if ($price <= 0) {
            return redirect('/girls');
        }
        DB::update('update servises set price = ? where name = \'toTop\'', [$price]);
        return $this->getAdminPanel();

    }

    public function testemail()
    {
        $testname = 'testname1';
        $mail = 'triest21@gmail.com';
        Mail::send('mail.test', ['name' => $testname], function ($message) use ($mail) {
            $message
                ->to($mail, 'some guy')
                ->from('sakura-testmail@sakura-city.info')
                ->subject('Welcome');
        });
    }

    public function sendmail()
    {
        $testname = '6422d6521c1a680f 79214623189';
        $mail = 'sms@atomic.center';
        Mail::send('mail.sms', ['name' => $testname], function ($message) use ($mail) {
            $message
                ->to($mail, 'some guy')
                ->from('sakura-testmail@sakura-city.info')
                ->subject('6422d6521c1a680f 79214623189');
        });
        echo 'senbded';
    }

    private function SendMesageTOConfernd($token, $mail)
    {
        Mail::send('email.confernemail', ['name' => 'testname', 'token' => $token], function ($message) use ($mail) {
            $message
                ->to($mail, 'sope person')
                ->from('sakura.city2@yandex.ru')
                ->subject('Подтвердите адрес электронной почты');
        });
    }

    public function MailtoConfurn()
    {
        $user = Auth::user();
        if ($user['email_token'] == null) {
            $token = str_random(16);
            DB::table('users')->where('id', $user->id)->update(['email_token' => $token]);
        } else {
            $token = $user['email_token'];
        }
        $mail = $user['email'];
        Mail::send('mail.confernemail', ['name' => 'testname', 'token' => $token], function ($message) use ($mail) {
            $message
                ->to($mail)
                ->from('sakura-testmail@sakura-city.info')
                ->subject('Подтвердите адрес электронной почты');
        });
        return $this->index();
    }

    public function conferndEmail($token)
    {
        $user = User::select('name', 'email', 'password', 'token', 'is_conferd', 'active')->where('email_token',
            $token)->first();
        if ($user != null) {
            $email = $user->email;
            $user['is_conferd'] = 1;
            $user->save();
            DB::update('update users set  	is_conferd =1 where email=?', [$email]);
            return $this->index();
        } else {
            return $this->index();
        }
        $requwest = new Request();
        return $this->createGirl($requwest);
    }

    public function SendSMS($phone, $text)
    {
        $src = '<?xml version="1.0" encoding="UTF-8"?>
        <SMS>
            <operations>
            <operation>SEND</operation>
            </operations>
            <authentification>
            <username>sakura-city@rambler.ru</username>
            <password>22d2af28</password>
            </authentification>
            <message>
            <sender>SMS</sender>
            <text>' . $text . '</text>
            </message>
            <numbers>
            <number messageID="msg11">' . $phone . '</number>
            </numbers>
        </SMS>';

        $Curl = curl_init();
        $CurlOptions = array(
            CURLOPT_URL => 'http://api.atompark.com/members/sms/xml.php',
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_POST => true,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_TIMEOUT => 100,
            CURLOPT_POSTFIELDS => array('XML' => $src),
        );
        curl_setopt_array($Curl, $CurlOptions);
        if (false === ($Result = curl_exec($Curl))) {
            throw new Exception('Http request failed');
        }
        curl_close($Curl);
    }

    public function rules()
    {
        return [
            'phone' => 'min:10|max:10=> \'Введите номер телефона, 10 цифр\'',
        ];
    }

    public function messages()
    {
        return [
            'required' => 'The :attribute field is required.'
        ];
    }

    public function inputPhone(Request $request)
    {
        $validatedData = $request->validate([
            'phone' => 'required|digits:11|numeric',
        ]);
        if (Auth::guest()) {
            return redirect('/login');
        }
        $user = Auth::user();
        $phone = $request['phone']; //dump($phone);
        //тут попробуем проверить, что=бы первая цифра была 7;
        $firstlettst = substr($phone, 0, 1);
        if ($firstlettst == 8) {
            $phone = substr_replace($phone, 7, 0, 1);
        }
        //проверяем, есть ли пользователь с этим телефоном;
        $user_with_this_phone = User::select('id', 'name', 'email', 'password', 'token', 'is_conferd', 'active',
            'money', 'isAdmin', 'email_token', 'phone', 'phone_conferd', 'actice_code', 'akcept')->where('phone',
            $phone)->first();
        if ($user_with_this_phone != null) {
            if ($user_with_this_phone->email != $user->email and $user_with_this_phone->phone_conferd == 0) {
                //   echo "не этот пользовател и его номер не подтвержден";
                DB::table('users')->where('email', $user_with_this_phone->email)->update(['phone' => null]);
                $user = Auth::user();
                $id = $user->id;
                DB::table('users')->where('id', $id)->update(['phone' => $phone]);
                $activeCode = rand(1000, 9999);
                DB::update('update users set actice_code = ? where id = ?', [$activeCode, $id]);
                $this->sendSMS($phone, $activeCode);

                return view('inputactivecode');
            } elseif ($user_with_this_phone->email != $user->email and $user_with_this_phone->phone_conferd == 1) {
                $validatedData = $request->validate([
                    'phone' => 'required|digits:11|numeric|unique:users',
                ]);

            }
        }
        $phoneinbase = collect(DB::select('select phone from users where id=?', [$user->id]))->first();
        if ($phoneinbase->phone != null) {
            $activecod = collect(DB::select('select actice_code from users where id=?', [$user->id]))->first();
            $this->sendSMS($phone, $activecod->actice_code);
            return view('inputactivecode');
        } else {
            if (Auth::guest()) {
                return redirect('/login');
            }
            $user = Auth::user();
            $id = $user->id;

            DB::table('users')->where('id', $id)->update(['phone' => $phone]);
            $activeCode = rand(1000, 9999);
            DB::update('update users set actice_code = ? where id = ?', [$activeCode, $id]);
            $this->sendSMS($phone, $activeCode);
            return view('inputactivecode');
        }


    }

    public function inputActiveCode(Request $request)
    {
        $code = $request['code'];
        if (Auth::guest()) {
            return redirect('/login');
        }
        $user = Auth::user();
        $id = $user->id;
        $user_code = collect(DB::select('select actice_code from users where id like ?', [$id]))->first();
        $code = (int)$code;
        $user_code = $user_code->actice_code;

        if ($code == $user_code) {
            DB::update('update users set phone_conferd  = 1 where id = ?', [$id]);
            return $this->index();
        } else {
            echo 'code actice false';
            return view("inputphone");
        }

    }

    public function akceptRules(Request $request)
    {
        if (Auth::guest()) {
            return redirect('/login');
        }
        $user = Auth::user();

        if ($user == null) {
            return redirect('/login');
        }
        $email = $user->email;
        if ($email == null) {
            return $this->index();
        }
        DB::table('users')->where('id', $user->id)->update(['akcept' => 1]);
        $user = Auth::user();
        if ($user->is_conferd == 0) {
            return view('conferntEmail')->with(['email' => $user->email]);
        }

        if ($user->phone == null) {
            return view('inputphone');
        }
        if ($user->phone_conferd == 0) {
            return view('inputphone');
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
            return $this->index();
        }


        $phone = $user->phone;
        $serveses = null;
        $title = "Создание анкеты";
        return view('createGirl')->with(['servises' => $serveses, 'title' => $title, 'phone' => $phone]);
    }



    public function inputPhone2()
    {
        return view('inputPhone2');
    }


    public function findProductName(Request $request)
    {
        $data = Product::select('productname', 'id')->where('prod_cat_id', $request->id)->take(100)->get();
        return response()->json($data);//then sent this data to ajax success
    }

    public function findRegions($id)
    {
        $regions = Region::select('name')
            ->where('id_country', $id)
            ->orderBy('id_region ', 'ASC')
            ->get();
        return Response::json($regions);
    }

    public function findPrice(Request $request)
    {

        //it will get price if its id match with product id
        $p = Product::select('price')->where('id', $request->id)->first();

        return response()->json($p);
    }

    public function getSearch()
    {
        $price = collect(DB::select('select price from servises '));

        $countries = collect(DB::select('select * from countries')); //получаем города
        return view('cityes')->with(['countries' => $countries]);;
    }

    public function search(Request $request)
    {
        $current_date = Carbon::now();
        if ($request['country'] == '-') {
            $girls = Girl::select([
                'id',
                'name',
                'login',
                'email',
                'phone',
                'main_image',
                'description'
            ])->simplePaginate(9);
            $vipGirls = Girl::select(['id', 'name', 'login', 'email', 'phone', 'main_image', 'description'])
                ->where('beginvip', '<', $current_date)
                ->where('endvip', '>', $current_date)
                ->orderBy('created_at', 'DESC')
                ->orderBy('rating', 'ASC')
                ->Paginate(9);

            $countries = collect(DB::select('select * from countries')); //получаем страны

            //получаем регионы
            $regions = collect(DB::select('select * from regions where id_country=1')); //получаем страны

            $cities = collect(DB::select('select * from cities where id_region=1'));
            return view('index2')->with([
                'girls' => $girls,
                'vipGirls' => $vipGirls,
                'countries' => $countries,
                'regions' => $regions,
                'cities' => $cities
            ]);

        }
        if ($request['city'] != null) {
            $girls = Girl::select(['id', 'name', 'login', 'email', 'phone', 'main_image', 'description', 'sex'])
                ->where('city_id', $request['city'])
                ->orderBy('rating', 'ASC')
                ->Paginate(9);
            $current_date = Carbon::now();
            $vipGirls = Girl::select(['id', 'name', 'login', 'email', 'phone', 'main_image', 'description'])
                ->where('beginvip', '<', $current_date)
                ->where('endvip', '>', $current_date)
                ->orderBy('created_at', 'DESC')
                ->orderBy('rating', 'ASC')
                ->Paginate(9);
            $countries = collect(DB::select('select * from countries')); //получаем страны

            //получаем регионы
            $regions = collect(DB::select('select * from regions where id_country=1')); //получаем страны
            $cities = collect(DB::select('select * from cities where id_region=1'));
            return view('index2')->with([
                'girls' => $girls,
                'vipGirls' => $vipGirls,
                'countries' => $countries,
                'regions' => $regions,
                'cities' => $cities
            ]);
        }

        if ($request['region'] != null) {

            $girls = Girl::select(['id', 'name', 'login', 'email', 'phone', 'main_image', 'description', 'sex'])
                ->where('region_id', $request['region'])
                ->orderBy('rating', 'ASC')
                ->Paginate(9);

            //   die();
            $current_date = Carbon::now();
            $vipGirls = Girl::select(['id', 'name', 'login', 'email', 'phone', 'main_image', 'description'])
                ->where('beginvip', '<', $current_date)
                ->where('endvip', '>', $current_date)
                ->orderBy('created_at', 'DESC')
                ->orderBy('rating', 'ASC')
                ->Paginate(9);
            $countries = collect(DB::select('select * from countries')); //получаем страны

            //получаем регионы
            $regions = collect(DB::select('select * from regions where id_country=1')); //получаем страны

            $cities = collect(DB::select('select * from cities where id_region=1'));
            return view('index2')->with([
                'girls' => $girls,
                'vipGirls' => $vipGirls,
                'countries' => $countries,
                'regions' => $regions,
                'cities' => $cities
            ]);
        }

        $girls = Girl::select(['id', 'name', 'login', 'email', 'phone', 'main_image', 'description', 'sex'])
            //  ->where('vip','=','1')
            ->orderBy('created_at', 'DESC')
            ->orderBy('rating', 'ASC')
            ->Paginate(9);
        $vipGirls = Girl::select(['id', 'name', 'login', 'email', 'phone', 'main_image', 'description'])
            ->where('beginvip', '<', $current_date)
            ->where('endvip', '>', $current_date)
            ->orderBy('created_at', 'DESC')
            ->orderBy('rating', 'ASC')
            ->Paginate(9);
        return view('index2')->with(['girls' => $girls, 'vipGirls' => $vipGirls]);

    }

    public function adminToGirl(Request $request)
    {

        if ($request->has('banned')) {
            $banned = 1;
        } else {
            $banned = 0;
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
            'age',
            'country_id',
            'region_id',
            'city_id',
            'banned'
        ])
            ->where('id', $request->girl)->first();
        $girl->banned = $banned;
        $girl->save();

        //теперь сообщение
        if ($request->description != null) {
            $message = new Message();
            $message->text = $request->description;
            $message->girl_id = $request->girl;
            $message->fromAdmin = 1;
            $message->save();
        }

        if ($banned == 0) {
            $text = "sakura-city.info. У вас новое сообщение от администратора! Ваша анкета разблокированна.";
        } elseif ($banned == 1) {
            $text = "sakura-city.info. У вас новое сообщение от администратора! Ваша анкета заблокированна.";
        }
        $this->SendSMS($girl->phone, $text);
        return $this->showGirl($girl->id);
    }

    public function getMessagePage()
    {
        if (Auth::guest()) {
            return redirect('/login');
        }
        $user = Auth::user();

        $girl = Girl::select(['id', 'name'])
            ->where('user_id', $user->id)->first();
        $messages = Message::select('id', 'text', 'girl_id', 'fromAdmin', 'date', 'readed')
            ->where('girl_id', $girl->id)
            ->orderBy('created_at')
            ->get();
        foreach ($messages as $message) {
            if ($message->fromAdmin == 1) {
                $message->readed = 1;
                $message->save();
            }
        }
        return view('messages')->with(['messages' => $messages, 'girl' => $girl]);
    }

    public function getMessagePageAdmin($girl_id)
    {
        if (Auth::guest()) {
            return redirect('/login');
        }
        $user = Auth::user();

        $girl = Girl::select(['id', 'name'])
            ->where('id', $girl_id)->first();
        $messages = Message::select('id', 'text', 'girl_id', 'fromAdmin', 'date', 'readed', 'adminreaded')
            ->where('girl_id', $girl->id)
            ->orderBy('created_at')
            ->get();
        foreach ($messages as $message) {
            if ($message->fromAdmin == 1) {
                $message->readed = 1;
                $message->save();
            }
        }
        foreach ($messages as $message) {
            $message->adminreaded = 1;
            $message->save();
        }
        return view('messages')->with(['messages' => $messages, 'girl' => $girl]);
    }

    public function girlToAdmin(Request $request)
    {
        $validatedData = $request->validate([
            'girl' => 'required',
            'description' => 'required'
        ]);
        if (Auth::guest()) {
            return redirect('/login');
        }
        $message = new Message();
        $message->text = $request->description;
        $message->girl_id = $request->girl;
        $message->fromAdmin = 0;
        $message->adminreaded = 0;
        $message->readed = 0;
        $message->save();
        return $this->getMessagePage();
    }

    public function usersList(Request $request)
    {
        $users = User::select([
            'id',
            'name',
            'email',
            'is_conferd',
            'money',
            'isAdmin',
            'phone',
            'phone_conferd',
            'actice_code',
            'akcept',
            'smsResetCode'
        ])
            ->simplePaginate(25);


        return view('users-list')->with(['users' => $users]);
    }

    public function messagesList(Request $request)
    {
        $messages = Message::select(['id', 'text', 'girl_id', 'date', 'adminreaded'])
            //->groupBY('girl_id')
            ->orderBY('date', 'desc', 'girl_id')
            ->get()
            ->unique('girl_id');
        $user = Auth::user();
        if ($user == null) {
            return $this->index();
        }
        if ($user->isAdmin == 0 or $user == null) {
            return $this->index();
        }
        return view('messages-list')->with(['messages' => $messages]);

    }

    public function moneyHistory(Request $request)
    {
        $user = Auth::user();
        if ($user == null) {
            return $this->index();
        }
        if ($user->isAdmin == 0 or $user == null) {
            return $this->index();
        }
        $history = DB::table('money_history')
            ->orderBy('date', 'DESC')
            ->paginate(50);
        return view('moneyhistory')->with(['history' => $history]);
    }

    public function SendResetSMS($phone)
    {
        return Response::json([
            'phone' => $phone,
        ]);
    }

    public function resetPassSMS(Request $request)
    {
        $rules = [
            'password' => 'requared|same:password-confirm|string|min:6|confirmed',
            'user' => 'requared'

        ];

        $user = User::select([
            'id',
            'name',
            'email',
            'password',
            'token',
            'is_conferd',
            'active',
            'money',
            'isAdmin',
            'email_token',
            'phone',
            'phone_conferd',
            'actice_code',
            'akcept',
            'smsResetCode'
        ])
            ->where('email_token', $request->user)
            ->first();
        $pass = $request['password'];
        $user['password'] = Hash::make($pass);
        $user->save();

        return $this->index();
    }


}
