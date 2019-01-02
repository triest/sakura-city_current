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
use phpDocumentor\Reflection\Types\Null_;
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
use App\Message;
use Mail;
use settype;
use App\Mail\Reminder;


class MessagesController extends Controller
{
    function getMessagesPage()
    {
        echo 'test';
        if (Auth::guest()) {
            return redirect('/login');
        }
        $user = Auth::user();
        if ($user == null) {
            return redirect('/login');
        }
        dump($user);
        $messages_sended = Message::select(['text', 'sender_id', 'resiver_id', 'date', 'status'])
            ->where('sender_id', $user->id)
            ->get();

        dump($messages_sended);
        $messages_resived = Message::select(['text', 'sender_id', 'resiver_id', 'date', 'status'])
            ->where('resiver_id', $user->id)
            ->get();

        dump($messages_resived);
        die();
        $outheher_user = $messages_sended->resiver_id;
        dump($outheher_user);
        $messages = Message::select(['text', 'sender_id', 'resiver_id', 'date', 'status'])
            ->Where('sender_id', $user->id)
            ->orWhere('resiver_id', $user->id)
            ->get();
        dump($messages);
        die();
        return view('messages.dialog')->with([
            'messages_sended' => $messages_sended,
            'messages_resived' => $messages_resived,
            'this_user' => $user,
            'outher_user' => $outheher_user
        ]);

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

}