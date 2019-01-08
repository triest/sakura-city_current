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


class AdminController extends Controller
{

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
}
