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
        $messages = DB::select('select ms.id,gl.name, gl.main_image,ms.user_from,ms.user_to,ms.msg,ms.created_at 
                from messages3 ms left join girls gl on ms.user_from=gl.user_id
        WHERE ms.user_from=? or ms.user_to=? 
         ', [Auth::user()->id, Auth::user()->id]
        );
        return $messages;
    }

    public static function getMyMessages($id)
    {
        /*   $messages = DB::table('messages3')->where('user_from', '=', Auth::user()->id)->orWhere('user_to', '=',
               $id)->orderBy('created_at')->get();*/
        $messages = DB::select('select ms.id,gl.name, gl.main_image,ms.user_from,ms.user_to,ms.msg,ms.created_at 
                from messages3 ms left join girls gl on ms.user_from=gl.user_id
        WHERE (ms.user_from=? and  ms.user_to=?) or (ms.user_from=? and  ms.user_to=?)  
         ', [Auth::user()->id, $id, $id, Auth::user()->id]
        );


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


}