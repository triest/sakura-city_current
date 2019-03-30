<?php

namespace App\Http\Controllers;

use App\Events\NewMessage;
use App\Events\newApplication;
use App\Girl;
use App\MyRequwest;
use App\Useruser;
use App\Dialog;
use Illuminate\Http\Request;
use App\User;
use App\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Nexmo\Response;
use Symfony\Component\HttpFoundation\Tests\NewRequest;


class ContactsController extends Controller
{
    public function get()
    {
        // get all users except the authenticated one
        //   $contacts = User::where('id', '!=', auth()->id())->get();
        // get a collection of items where sender_id is the user who sent us a message
        // and messages_count is the number of unread messages we have from him
        $dialogs = Dialog::select('id', 'my_id', 'other_id')->where('my_id', Auth::user()->id)->get();

        //  dump($dialogs);
        $contacts = [];
        foreach ($dialogs as $dialog) {
            $other = $dialog->other_id;

            $user = User::select()->where('id', $other)->first();

            array_push($contacts, $user);
        }

        return response()->json($contacts);
    }

    public function get2()
    {
        // get all users except the authenticated one
        // $contacts = User::where('id', '!=', auth()->id())->get();
        // get a collection of items where sender_id is the user who sent us a message
        // and messages_count is the number of unread messages we have from him
        //  $contacts = null;
        $contacts = DB::select('select * from conversation con left join girls gl on gl.user_id=con.user_two where con.user_one=?',
            [Auth::user()->id]);

        return response()->json($contacts);
    }

    public function getMessagesFor($id)
    {
        Message::where('from', $id)->where('to', auth()->id())->update(['readed' => true]);
        // mark all messages with the selected contact as read
        Message::where('from', $id)->where('to', auth()->id());
        // get all messages between the authenticated user and the selected user
        $messages = Message::where('from', $id)->orWhere('to', $id)->get();

        return response()->json($messages);
    }


    public function send(Request $request)
    {
        $message = Message::create([
            'from' => auth()->id(),
            'to' => $request->contact_id,
            'text' => $request->text,
        ]);

        $user = Auth::user();
        $id2 = $request->contact_id;
        $dialog = Dialog::select(['id', 'my_id', 'other_id'])->where('my_id', $user->id)->where('other_id',
            $id2)->first();
        if ($dialog == null) {
            $dialog3 = new Dialog();
            $dialog3->my_id = $user->id;
            $dialog3->other_id = $id2;
            $dialog3->save();
        }
        $dialog2 = Dialog::select(['id', 'my_id', 'other_id'])->where('other_id', $user->id)->where('my_id',
            $id2)->first();
        if ($dialog2 == null) {
            $dialog4 = new Dialog();
            $dialog4->other_id = $user->id;
            $dialog4->my_id = $id2;
            $dialog4->save();
        }
        broadcast(new NewMessage($message));

        return response()->json($message);
    }

    public function getCountUnreadedMessages(Request $request)
    {
        $user = Auth::user();
        $messages = Message::where('to', $user->id)->where('readed', 0)->get();
        $count = count($messages);

        return $count;
    }

    //get application page
    public function getApplicationPage()
    {
        return view('application');
    }


    public function getApplication()
    {
        //просмотр запросов

        $user = Auth::user();
        //  $request = collect(DB::select('select * from requwest where target_id=?', [$user->id]));
        $request = MyRequwest::select('id',
            'who_id',
            'target_id')->where('target_id', $user->id)
            ->where('readed', 0)
            ->get();
        $array = [];
        foreach ($request as $item) {
            $girl = Girl::select(['id', 'name', 'main_image'])->where('user_id', $item->who_id)->first();
            array_push($array, $girl);
        }

        return $array;
    }

    //запросы для меня
    public function myApplication()
    {
        //просмотр запросов

        $user = Auth::user();
        //  $request = collect(DB::select('select * from requwest where target_id=?', [$user->id]));
        $request = MyRequwest::select('id',
            'who_id',
            'target_id', 'who_name', 'image', 'status', 'readed')->where('who_id', $user->id)
            //->where('readed', 0)
            ->get();
        $array = [];
        /*  foreach ($request as $item) {
              $girl = Girl::select(['id', 'name', 'main_image'])->where('user_id', $item->who_id)->first();
              array_push($array, $girl);
          }*/

        //  return response()->json(, $request);
        return $request;
    }

    public function denideAccess(Request $request)
    {
        $id = $request->input('id');

        $auth = Auth::user();
        $girl = Girl::select(['id', 'user_id'])->where('id', $id)->first();


        $user_id = $girl->user_id;
        //получаем запрос
        $myrequest = MyRequwest::select('id',
            'who_id',
            'target_id', 'status', 'readed')->where('target_id', $auth->id)
            ->where('who_id', $user_id)
            ->first();

        //ставим статус закрыто
        $myrequest->readed = 1;
        $myrequest->status = 'rejected';
        $myrequest->save();

        return response()->json(['ok']);
    }

    public function makeAccess(Request $request)
    {
        $id = $request->input('id');
        $auth = Auth::user();
        $girl = Girl::select(['id', 'user_id'])->where('id', $id)->first();
        $id = $girl->user_id;
        //получаем запрос
        $myrequest = MyRequwest::select('id',
            'who_id',
            'target_id', 'status', 'readed')->where('target_id', $auth->id)
            ->where('who_id', $id)
            ->first();
        //ставим статус закрыто
        $myrequest->readed = 1;
        $myrequest->status = 'confirmed';
        $myrequest->save();
        DB::table('user_user')
            ->insert(['other_id' => $auth->id, 'my_id' => $id]);

        return response()->json(['ok']);
    }

    public function reqTest(Request $request)
    {
        $auth = Auth::user();
        $myrequest = MyRequwest::select('id',
            'who_id',
            'target_id', 'status', 'readed')->where('target_id', 16)
            ->where('who_id', 1)
            ->first();

        broadcast(new newApplication($myrequest));
    }

    public function getCountUnreadedRequwest(Request $request)
    {
        $auth = Auth::user();
        $myrequest = MyRequwest::select('id',
            'who_id',
            'target_id', 'status', 'readed')->where('target_id', $auth->id)
            ->where('readed', 0)
            ->get();
        $count = count($myrequest);

        return $count;
    }

    public function getIsPrivateOrNot(Request $request)
    {
        // dump($request);
        $id = $request->input('id');
        $auth = Auth::user();
        $private = DB::table('user_user')
            ->where('my_id', $auth->id)
            ->where('other_id', $id)
            ->first();
        if ($private == null) {
            return response()->json('false');  //
        } else {
            return response()->json('true');
        }
    }

    public function sendornot(Request $request)
    {
        $id = $request->input('id');
        $girl = Girl::select('id', 'user_id')->where('id', $id)->first();
        $auth = Auth::user();

        $myrequest = MyRequwest::select('id',
            'who_id',
            'target_id', 'status', 'readed')->where('target_id', $girl->user_id)
            ->where('who_id', $auth->id)->first();
        //  dump($myrequest);
        if ($myrequest != null) {
            return $myrequest;
        } else {
            return response()->json('not');
        }

    }

    //отправляет запрос на открытие анкеты
    public function sendreg(Request $request)
    {
        $id = $request->input('id');
        $girl = Girl::select(['id', 'user_id'])->where('id', $id)->first();

        $id = $girl->user_id;
        $auth = Auth::user();

        $girl2 = Girl::select(['name', 'main_image'])->where('user_id', $auth->id)->first();
        $myrequest = new MyRequwest();
        $myrequest->who_id = $auth->id;
        $myrequest->target_id = $id;
        $myrequest->who_name = $girl2->name;
        $myrequest->image = $girl2->main_image;
        $myrequest->save();
        broadcast(new newApplication($myrequest));

        return response()->json('ok');
    }

    public function whoHavaAccessToMyAnket(Request $request)
    {
        $auth = Auth::user();

        $users_id = Useruser::select('id', 'my_id', 'other_id')->where(
            'other_id',
            $auth->id
        )->get();//поулчаем список пользоваьедей, у которых доступ к моей анкете

        //   dump($users_id);
        $array = [];
        foreach ($users_id as $item) {
            $girl = Girl::select(['id', 'name', 'main_image'])->where('user_id', $item->my_id)->first();
            array_push($array, $girl);
        }

        return $array;
    }

    //закрыть доступ
    public function clouseaccess(Request $request)
    {
        $id = $request->input('id');
        $auth = Auth::user();
        $girl = Girl::select(['id', 'user_id'])->where('id', $id)->first();
        $id = $girl->user_id;
        //dump($id);
        DB::table('user_user')->where('my_id', '=', $id)->where('other_id', $auth->id)->delete();
        DB::table('requwest')->where('who_id', '=', $id)->where('target_id', $auth->id)->delete();

        return response()->json('ok');
    }

    public function getUserID(Request $request)
    {
        $id = $request->input('id');
        //dump($id);
        $girl = Girl::select('id', 'user_id')->where('id', $id)->first();
        if ($girl != null) {
            return \response()->json($girl->user_id);
        } else {
            return null;
        }
        // return  response()->json($id);
    }


}