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
use App\MyRequwest;
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
class PrivateController extends Controller
{

    public function makeRequwest($id)
    {
        dump($id);
        $authUser = Auth::user();
        if ($authUser == null) {
            return null;
        }

        $girl = Girl::select([
            'name',
            'email',
            'password',
            'id',
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
            'banned',
            'user_id'
        ])->where('id', $id)->first();

        dump($girl);

        $user = User::select('id', 'name', 'email')->where('id',
            $girl->user_id)->first();
        echo "auth user";

        $requwest = new MyRequwest();
        //  $requwest->who()->save($authUser);
      //  $requwest->who()->save($authUser);
            $requwest->who_id=$authUser->id;
            $requwest->target_id=$user->id;
   //     $requwest->target()->save($user);
        $requwest->save();
        //return redirect('/anket/{id}',$user->id);
        return redirect()->route('showGirl', ['id' => $girl->id]);

    }

}