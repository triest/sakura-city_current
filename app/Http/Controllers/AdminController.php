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

}
