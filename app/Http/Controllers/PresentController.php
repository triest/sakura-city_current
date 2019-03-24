<?php

namespace App\Http\Controllers;

use App\Events\eventPreasent;
use App\GiftAct;
use App\Girl;
use App\Present;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use File;
use Illuminate\Support\Facades\DB;

class PresentController extends Controller
{
    //
    //получае список подарков
    public function getpresents()
    {
        $presents = Present::select([
            'id',
            'name',
            'price',
            'image',
        ])->get();

        return response()->json([$presents]);
    }

    //создаём подарок
    public function storepresent(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'price' => 'required|numeric|min:1',
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $present = new Present();
        $present->name = $request->name;
        $present->price = $request->price;
        if (Input::hasFile('file')) {
            $image_extension = $request->file('file')->getClientOriginalExtension();
            $image_new_name = md5(microtime(true));
            $temp_file = base_path().'/public/presents/upload/'.strtolower($image_new_name.'.'.$image_extension);// кладем файл с новыс именем
            $request->file('file')
                ->move(base_path().'/public/presents/upload/',
                    strtolower($image_new_name.'.'.$image_extension));

            $present->image = $image_new_name.'.'.$image_extension;
            //сохраняем уменьшенную копию
            $small = base_path().'/public/presents/small/'.strtolower($image_new_name.'.'.$image_extension);
            copy($temp_file, $small);

            $image = new ImageResize($small);
            $image->resizeToHeight(150);
            $image->save($small);
        }
        $present->save();

        return response()->json(['ok']);
    }

    public function delpresent(Request $request)
    {
        $id = $request->id;

        $presentss = Present::select([
            'id',
            'name',
            'price',
            'image',
        ])->where('id', $id)
            ->first();
        if ($presentss == null) {
            return response()->json(['fail']);
        }

        // удаляем файд
        try {
            $path = base_path().'/public/presents/upload/'.$presentss->image;
            File::Delete($path);
            $path = base_path().'/public/presents/small/'.$presentss->image;
            File::Delete($path);

        } catch (\Exception $e) {
            echo "delete errod";
        }
        $presentss->delete();

        return response()->json(['ok']);
    }

    public function givepresent(Request $request)
    {
        $who = Auth::user();
        // $target = User::select(['id', 'name'])->where('id', $request->user_id)->first();
        $girl = Girl::select(['id', 'user_id'])->where('id', $request->user_id)->first();
        $target = User::select(['id'])->where('id', $girl->user_id)->first();
        $gift = Present::select(['id'])->where('id', $request->present_id)->first();
        $giftAct = new GiftAct();
        //   $giftAct->who()->save($who);
        $giftAct->who_id = $who->id;
        $giftAct->target_id = $target->id;
        $giftAct->present_id = $gift->id;
        $giftAct->save();

        broadcast(new eventPreasent($giftAct));

        return response()->json(['ok']);
    }

    public function presenttest(Request $request)
    {

        //   $giftAct->who()->save($who);
        /* $giftAct->who_id = 2;
         $giftAct->target_id = 1;
         $giftAct->present_id = 1;
         $giftAct->save();*/


        $giftAct = GiftAct::select(['id', 'present_id', 'who_id', 'target_id'])->where('id', 1)->first();
        dump($giftAct);
        broadcast(new eventPreasent($giftAct));
    }

    public function getCountUnreaderPresents(Request $request)
    {
        $user = Auth::user();
        $gift = GiftAct::select('id')->where('target_id', $user->id)->where('readed', 0)->get();
        $gift = count($gift);

        return $gift;
    }

    public function presentsForMe()
    {
        $user = Auth::user();
        // $giftAct = GiftAct::select('id','present_id','who_id','target_id','created_at','readed')->where('target_id', $user->id)->where('readed', 0)->get();
        $present = DB::select("SELECT act.id as 'act_id' ,pres.id as 'id', who.id as 'who_id',who.name as 'who_name',girl.main_image as 'who_image',pres.id as 'pres_id',pres.name as 'pres_name',pres.image as 'pres_image',act.readed 
                FROM gift_act act left join presents pres on act.present_id=pres.id
            left join users who  on who.id=act.who_id
                       left join girls girl on girl.user_id=who.id
                       where act.target_id=:id and act.readed=0
                       ", ['id' => $user->id]);

        return response()->json([$present]);
    }

    public function getpresentsHistoryforMe()
    {
        $user = Auth::user();
        // $giftAct = GiftAct::select('id','present_id','who_id','target_id','created_at','readed')->where('target_id', $user->id)->where('readed', 0)->get();
        $present = DB::select("SELECT act.id as 'act_id' ,act.created_at as 'sended', pres.id as 'id', who.id as 'who_id',who.name as 'who_name',girl.main_image as 'who_image',pres.id as 'pres_id',pres.name as 'pres_name',pres.image as 'pres_image',act.readed 
                FROM gift_act act left join presents pres on act.present_id=pres.id
            left join users who  on who.id=act.who_id
                       left join girls girl on girl.user_id=who.id
                       where act.target_id=:id
                       ", ['id' => $user->id]);

        return response()->json([$present]);
    }

    public function getpresentsFromMe()
    {
        $user = Auth::user();
        // $giftAct = GiftAct::select('id','present_id','who_id','target_id','created_at','readed')->where('target_id', $user->id)->where('readed', 0)->get();
        $present = DB::select("SELECT act.id as 'act_id' ,act.created_at as 'sended', pres.id as 'id', who.id as 'who_id',who.name as 'who_name',girl.main_image as 'who_image',pres.id as 'pres_id',pres.name as 'pres_name',pres.image as 'pres_image',act.readed 
                FROM gift_act act left join presents pres on act.present_id=pres.id
            left join users who  on who.id=act.who_id
                       left join girls girl on girl.user_id=who.id
                       where act.who_id=:id
                       ", ['id' => $user->id]);

        return response()->json([$present]);
    }

    public function markpresentasreaded(Request $request)
    {
        dump($request);
        $present = GiftAct::select(['id', 'present_id', 'who_id', 'target_id', 'readed'])->where('id',
            $request->present_id)->first();
        dump($present);
        if ($present != null) {
            $present->readed = true;
            $present->save();

            return response()->json(['ok']);
        }

        return response()->json(['fail']);
    }
}
