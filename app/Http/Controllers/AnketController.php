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


class AnketController extends Controller
{
    function index()
    {
        echo "index";
    }

    public function girlsShowAuchAnket(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $user_id = $user->id;
            $girl = Girl::select([
                'id',
                'name',
                'login',
                'email',
                'phone',
                'main_image',
                'description',
                'money',
                'beginvip',
                'endvip'
            ])->where('user_id', $user_id)->first();
            if ($girl == null) {
                $requwest = new Request();
                return $this->createGirl($requwest);
            }
            $id = $girl->id;
            /*тут рассичаем на сколько дней уму випа хватит*/
            $price_toTop = collect(DB::select('select price from servises where name=\'toTop\' '))->first(); //получили цену
            $money = collect(DB::select('select money from users where id=? ', [$user_id]))->first(); //получили цену

            $maxDay = $money->money / $price_toTop->price;
            $maxDay = floor($maxDay);
            $priceFirstPlase = collect(DB::select('select price from servises where name=\'toFirstPlase\' '))->first();
            $priceTop = collect(DB::select('select price from servises where name=\'toTop\' '))->first();
            $images = Photo::select(['id', 'photo_name'])->where('girl_id', $id)->get();
            return view('powerView')->with([
                'girl' => $girl,
                'images' => $images,
                'user' => $user,
                'max_day' => $maxDay,
                'priceFirstPlase' => $priceFirstPlase,
                'priceTop' => $priceTop
            ]);
        } else {
            return redirect('/girls');
        }
    }

    function createGirl(Request $request)
    {
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
        }
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
            return view('inputphone');
        }
        if ($user->phone_conferd == 0) {
            return view('inputphone');
        }
        //проверяем, вдруг анкета уже есть.
        if ($girl != null) {
            return $this->index();
        }


        $phone = $user->phone;
        $countries = collect(DB::select('select * from countries')); //получаем страны

        //получаем регионы
        $regions = collect(DB::select('select * from regions where id_country=1')); //получаем страны

        $cities = collect(DB::select('select * from cities where id_region=1'));

        $title = "Создание анкеты";
        return view('createGirl')->with([
                'servises' => $serveses,
                'title' => $title,
                'phone' => $phone,
                'countries' => $countries,
                'regions' => $regions,
                'cities' => $cities
            ]
        );
    }

    public function Store(Request $request)
    {

        // для начала проверим, есть ли созданная этим юзером анкета.
        $validatedData = $request->validate([
            'name' => 'required',

            'sex' => 'required',
            'age' => 'required|numeric|min:18',
            'met' => 'required',
            'description' => 'required',
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ]);

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
        }


        if ($request->has('famele')) {
            $sex = 'famele';
        }
        if ($request->has('male')) {
            $sex = 'male';
        }

        if (Input::hasFile('file')) {
            $image_extension = $request->file('file')->getClientOriginalExtension();
            $image_new_name = md5(microtime(true));
            $temp_file = base_path() . '/public/images/upload/' . strtolower($image_new_name . '.' . $image_extension);// кладем файл с новыс именем
            $request->file('file')
                ->move(base_path() . '/public/images/upload/', strtolower($image_new_name . '.' . $image_extension));
            $origin_size = getimagesize($temp_file);
        }

        $data = $request->all();
        $girl = new Girl();
        $girl->fill($data);
        $girl['main_image'] = $image_new_name . '.' . $image_extension;
        $girl['enabled'] = true;
        $id = Auth::user()->id;
        $girl['user_id'] = $id;
        $girl['age'] = $request['age'];
        $girl['sex'] = $request['sex'];

        $girl['meet'] = $request['met'];
        //встречи
        //местоположение

        $girl->save();

        if ($request->has('country')) {
            $country = $request['country'];
            if ($country == "-") {
                $country = null;
            }
            DB::table('girls')->where('id', $girl->id)->update(['country_id' => $country]);
        }

        if ($request->has('region')) {
            $region = $request['region'];

            if ($region == "-") {
                $region = null;
            }
            if ($region != null) {
                $girl['region_id'] = $region;
            }
            DB::table('girls')->where('id', $girl->id)->update(['region_id' => $region]);
        }


        if ($request->has('city')) {
            if ($request['city'] != null) {
                $city = $request['city'];
                if ($city == "-") {
                    $city = null;
                };
                DB::table('girls')->where('id', $girl->id)->update(['city_id' => $city]);
            }
        }

        $girl->save();

        if (Input::hasFile('images')) {
            $count = 0;
            foreach ($request->images as $key) {
                $image_extension = $request->file('file')->getClientOriginalExtension();
                $image_new_name = md5(microtime(true));
                $key->move(public_path() . '/images/upload/', strtolower($image_new_name . '.' . $image_extension));
                $id = $girl['id'];
                $photo = new Photo();
                $photo['photo_name'] = $image_new_name . '.' . $image_extension;
                $photo['girl_id'] = $id;
                $photo->save();
            }
        }
        return redirect('/girls');
    }

    public function galarayView(Request $request)
    {
        $user = Auth::user();
        if (Auth::guest()) {
            return redirect('/login');
        }

        if ($user == null) {
            return redirect('/login');
        }


        $girl = Girl::select([
            'id',
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
        ])->where('user_id', $user->id)->first();

        if ($girl == null) {
            return $this->index();
        }
        $images = Photo::select(['id', 'photo_name'])->where('girl_id', $girl->id)->get();
        return view('editImage')->with(['girl' => $girl, 'images' => $images]);

    }

    public function deleteImage($id)
    {
        $user = Auth::user();
        if (Auth::guest()) {
            return redirect('/login');
        }
        if ($user == null) {
            return redirect('/login');
        }
        $girl = Girl::select([
            'id',
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
        ])->where('user_id', $user->id)->first();
        if ($girl == null) {
            return $this->index();
        }
        $temp_file = base_path() . '/public/images/upload/' . $id;// кладем файл с новыс именем
        try {
            $temp_file = base_path() . '/public/images/upload/' . $id;
            File::Delete($temp_file);
            // тут будем удалять из таблицы
            $photo = Photo::select('id')->where('photo_name', $id)->get();
            $photo->delete();

        } catch (\Exception $e) {
            echo "delete errod";
        }

        $image = Photo::select(['id', 'photo_name'])->where('photo_name', $id)->first();
        try {
            File::delete($id);
        } catch (IOException $e) {
        }
        $image->delete();
        $requwest = new Request();
        return $this->galarayView($requwest);
    }

    public function uploadimage(Request $request)
    {
        $validatedData = $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ]);
        $user = Auth::user();
        if (Auth::guest()) {
            return redirect('/login');
        }
        if ($user == null) {
            return redirect('/login');
        }
        $girl = Girl::select([
            'id',
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
        ])->where('user_id', $user->id)->first();
        if ($girl == null) {
            return $this->index();
        }
        if (Input::hasFile('file')) {

            $image_extension = $request->file('file')->getClientOriginalExtension();
            $image_new_name = md5(microtime(true));
            $temp_file = base_path() . '/public/images/upload/' . strtolower($image_new_name . '.' . $image_extension);// кладем файл с новыс именем
            $request->file('file')
                ->move(base_path() . '/public/images/upload/', strtolower($image_new_name . '.' . $image_extension));
            $photo = new Photo();
            $girl = Girl::select('id')->where('user_id', $user->id)->first();
            $photo['photo_name'] = $image_new_name . '.' . $image_extension;
            //    $photo['girl_id'] = $girl->id;

            $photo->save();
        }
        $requwest = new Request();
        return $this->galarayView($requwest);
    }

    public function girlsEditAuchAnket()
    {
        if (Auth::guest()) {
            return redirect('/login');
        }
        $user = Auth::user();
        if ($user == null) {
            return redirect('/login');
        }
        $girl = Girl::select([
            'name',
            'age',
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
            'country_id',
            'region_id',
            'city_id'
        ])->where('user_id', $user->id)->first();
        if ($girl == null) {
            return $this->index();
        }
        $phone = $user->phone;
        $countries = collect(DB::select('select * from countries'));
        $regions = collect(DB::select('SELECT `id`, `id_region`, `id_country`, `name` FROM `regions` where `id_country`=?',
            [$girl->country_id]));
        $region = collect(DB::select('select * from regions where id=?',
            [$girl->region_id]))->first(); //получаем страны
        $city = collect(DB::select('select * from cities where id=?',
            [$girl->city_id]))->first();
        $country = collect(DB::select('select * from countries where id_country=?',
            [$girl->country_id]))->first(); //получаем страны
        $cityes = collect(DB::select('select * from `cities` where `id_region`=?', [$girl->region_id]));
        dump($girl);
        dump($cityes);
       
        return view('editGirl')->with([
            'girl' => $girl,
            'phone' => $phone,
            'countries' => $countries,
            'regions' => $regions,
            'cityes' => $cityes,
            'city' => $city,
            'region' => $region,
            'country' => $country
        ]);
    }

    public function edit(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'sex' => 'required',
            'age' => 'required|numeric|min:18',
            'met' => 'required',
            'description' => 'required',
            'file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ]);

        if (Auth::guest()) {
            return redirect('/login');
        }
        $user = Auth::user();
        if ($user == null) {
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
            'height'
        ])->where('user_id', $user->id)->first();

        if ($girl == null) {
            return redirect('/index');
        }
        if ($request->has('famele')) {
            $sex = 'famele';
        }
        if ($request->has('male')) {
            $sex = 'male';
        }

        DB::table('girls')->where('id', $girl->id)->update(['age' => $request['age']]);
        DB::table('girls')->where('id', $girl->id)->update(['sex' => $request['sex']]);
        DB::table('girls')->where('id', $girl->id)->update(['meet' => $request['met']]);
        DB::table('girls')->where('id', $girl->id)->update(['description' => $request['description']]);

        if (Input::hasFile('file')) {
            $old_image_name = $girl['main_image'];
            $path = base_path() . '/public/images/upload/' . $old_image_name;
            File::Delete($path);
            $image_extension = $request->file('file')->getClientOriginalExtension();
            $image_new_name = md5(microtime(true));
            $temp_file = base_path() . '/public/images/upload/' . strtolower($image_new_name . '.' . $image_extension);// кладем файл с новыс именем
            $new_name = $image_new_name . '.' . $image_extension;
            $request->file('file')
                ->move(base_path() . '/public/images/upload/', strtolower($image_new_name . '.' . $image_extension));

            DB::table('girls')->where('id', $girl->id)->update(['main_image' => $new_name]);
            $origin_size = getimagesize($temp_file);

        }


        //тут местоположее
        if ($request->has('country')) {
            $country = $request['country'];
            if ($country == "-") {
                $country = null;
            }
            DB::table('girls')->where('id', $girl->id)->update(['country_id' => $country]);
        }

        if ($request->has('region')) {
            $region = $request['region'];

            if ($region == "-") {
                $region = null;
            }
            if ($region != null) {
                $girl['region_id'] = $region;
            }
            DB::table('girls')->where('id', $girl->id)->update(['region_id' => $region]);
        }


        if ($request->has('city')) {
            if ($request['city'] != null) {
                $city = $request['city'];
                if ($city == "-") {
                    $city = null;
                };
                DB::table('girls')->where('id', $girl->id)->update(['city_id' => $city]);
            }
        }


        return $this->girlsEditAuchAnket();
    }
}
