<?php

namespace App\Http\Controllers;

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
use Symfony\Component\Filesystem\Exception\IOException;
use function Symfony\Component\VarDumper\Tests\Caster\reflectionParameterFixture;
//use Zend\InputFilter\Input;
use Illuminate\Support\Facades\Input;
use Auth;
use Illuminate\Contracts\Auth\Guard;

use App\Repositories\ImageRepository;
use Carbon\Carbon;


use App\User;
use App\Girl;
use App\Services;
use App\DemoMail;
use App\Region;
use App\City;
use App\Country;

use App\Mail\Reminder;

use App\Events\MessageSent;


class ChatController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show chats
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('chat.chat');
    }

    /**
     * Fetch all messages
     *
     * @return Message
     */
    public function fetchMessages()
    {
        return Message::with('user')->get();
    }

    /**
     * Persist message to database
     *
     * @param  Request $request
     * @return Response
     */
    public function sendMessage(Request $request)
    {
        $user = Auth::user();

        $message = $user->messages()->create([
            'message' => $request->input('message')
        ]);

        broadcast(new MessageSent($user, $message))->toOthers();

        return ['status' => 'Message Sent!'];
    }

    public function getAllMyMessages(Request $request)
    {
        /*$messages = DB::table('messages3')->where('user_from', '=', Auth::user()->id)->orWhere('user_to', '=',
            Auth::user()->id)->orderBy('created_at')->get();*/

//        $messages=DB::select('select gl.id, gl.main_image from girls gl right join messages3 ms on ms.from_id=gl.id');
        $dialogs = DB::select('select * from conversation con left join girls gl on gl.user_id=? where user_one=?',
            [Auth::user()->id, Auth::user()->id]);
        $dialogs = DB::select('select * from conversation con left join girls gl on gl.user_id=con.user_two where con.user_one=?',
            [Auth::user()->id]);
       //   dump($dialogs); //в dialogs -user two- другой пользователь


        return $dialogs;
    }

    public static function getMyMessages($id)
    {
        $girl=Girl::select('id','user_id')->where('id',$id)->first();

        $messages = DB::table('messages3')->where('user_from', '=', Auth::user()->id)->orWhere('user_to', '=',
            $id)->orderBy('created_at')->get();
          $messages = DB::select('select ms.id,gl.name, gl.main_image,ms.user_from,ms.user_to,ms.msg,ms.created_at
                  from messages3 ms left join girls gl on ms.user_from=gl.user_id
          WHERE (ms.user_from=? and  ms.user_to=?) or (ms.user_from=? and  ms.user_to=?)
           ', [Auth::user()->id, $girl->user_id, $girl->user_id, Auth::user()->id]
          );
       //       dump($messages);


        return $messages;
    }

    public static function getNameAndImage($id)
    {
        $anket = Girl::select([
            'name',
            'id',
            'main_image'
        ])->where('id', $id)->first();
        $anket = DB::select('select gl.main_image,ms.user_from,ms.user_to,ms.ms,ms.created_at from messages3 ms left join gils gl on ms.user_from=gl.id');

        return $anket;
    }

    public function sendMyMessages(Request $request)
    {
        dump($request);
        $autch = Auth::user();
        $sendMB = DB::table('messages3')->insert([
            'user_from' => $autch->id,
            'user_to' => $request->to,
            'msg' => $request->msg,
            'status' => 1
        ]);


        //в отдельную таблицу записываем диалоги


        $check = DB::select('select * from conversation where user_one=? and user_two=?', [$autch->id, $request->to]);

        if ($check == null) {
            $sendMB = DB::table('conversation')->insert([
                'user_one' => $autch->id,
                'user_two' => $request->to,
            ]);
        }
        $check = null;

        $check = DB::select('select * from conversation where user_one=? and user_two=?', [$request->to, $autch->id]);

        if ($check == null) {
            $sendMB = DB::table('conversation')->insert([
                'user_one' => $request->to,
                'user_two' => $autch->id,
            ]);
        }
        if ($sendMB) {
            return ['status' => 'Message Sent!'];
        } else {
            return ['status' => 'Fail!'];
        }
    }

}