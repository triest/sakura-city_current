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
        $user = User::select('id', 'name', 'email')->where('id',
            $girl->user_id)->first();
        $requwest = new MyRequwest();
        $requwest->who_id = $authUser->id;
        $requwest->target_id = $user->id;
        $requwest->save();
        return redirect()->route('showGirl', ['id' => $girl->id]);
    }

    //просмотр запросов
    public function requwestForMe(Request $request)
    {

        $user = Auth::user();
        $request = collect(DB::select('select * from requwest where target_id=?', [$user->id]));

        $request = MyRequwest::select('id',
            'who_id',
            'target_id')->where('target_id', $user->id)
            ->where('rezult', 'not_dispersed')
            ->get();
        //    dump($request);

        $myRequwest = MyRequwest::select('id',
            'who_id',
            'target_id', 'rezult')->where('who_id', $user->id)
            ->get();


        return view('requwest.myRequwest')->with(['requwest' => $request, 'myRequwest' => $myRequwest]);
    }

    public function makeAccess($id)
    {

        $authUser = Auth::user();
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
        if ($girl == null) {
            return Redirecr('/ankets');
        }
        DB::table('user_user')
            ->insert(['other_id' => $authUser->id, 'my_id' => $girl->user_id]);
        //обновляем таблицу запросов
        /*     DB::table('requwest')
                 ->where(['target_id', $authUser->id])
                 ->update(['rezult' => 'accepted'])->andWhere(['who_id', $id]);*/
        DB::update('update requwest set rezult = \'accepted\' where who_id = ? and	target_id=?',
            [$girl->user_id, $authUser->id]);


//запросу:
        //not_dispersed
        //accepted
        //denided
        return Redirect('/requwestForMe');
    }

    public function denideAccess($id)
    {
        $authUser = Auth::user();
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
        if ($girl == null) {
            return Redirecr('/ankets');
        }
        DB::table('requwest')
            ->where('target_id', '=', $authUser->id)
            ->where('who_id', '=', $id)
            ->update(['rezult' => 'denide']);


//запросу:
        //not_dispersed
        //accepted
        //denided
        return Redirect('/requwestForMe');
    }

    public function whoMakeSeeMyAnket()
    {
        $authUser = Auth::user();
        $request = collect(DB::select('select * from user_user where my_id=?', [$authUser->id]));
        $girl_array = [];
        foreach ($request as $item) {
            $user = User::select(['id', 'name'])->where('id', $item->other_id)->first();
            $girl = Girl::select(['id', 'name', 'main_image'])->where('user_id', $user->id)->first();
            array_push($girl_array, $girl);
        }
        return view('requwest.whoCanSee')->with(['requwest' => $request, 'girls' => $girl_array]);
    }

    public function clouseAccess($id)
    {
        $authUser = Auth::user();
        if ($authUser==null){
            return Redirect('/anket');
        }
        $girl = Girl::select([
            'user_id'
        ])->where('id', $id)->first();
     DB::delete('delete from user_user where my_id = ? and other_id = ? limit 1', [$authUser->id, $girl->user_id]);

        return Redirect('whoMakeSeeMyAnket');
    }
}