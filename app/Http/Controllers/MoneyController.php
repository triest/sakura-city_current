<?php

namespace App\Http\Controllers;

use App\Girl;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use File;
use Illuminate\Support\Facades\DB;

class MoneyController extends Controller
{
    //
    public function getCurrentMoney(Request $request)
    {
        $user = Auth::user();
        $money = $user->money;

        return response()->json(['money' => $money]);
    }

    public function reciverMoney(Request $request)
    {
        $date = Carbon::now();
        File::append(base_path().'/public/file.txt', 'data2'.PHP_EOL);
        File::append(base_path().'/public/file.txt',
            'oprration_id:'.$request['operation_id'].','.'datetime: '.$request['datetime'].','.$request['sha1_hash'].','.$request['withdraw_amount'].',label:'.$request['label'].','.$date.PHP_EOL);

        $operation_id = $request['operation_id'];
        if ($operation_id == 'test-notification') {
            try {
                $rez = DB::table('money_history')->insert([
                    'date' => $request->datetime,
                    'lable' => $request->lable,
                    'amount' => $request->amount,
                ]);
            } catch (IOException $exceptione) {
            }
        }
        $user = User::select(['id', 'email', 'name', 'money'])->where(['email' => $request->label])->first();
        $ammount = $request->amount;
        $user_money = $user->money;
        if ($user != null && $user_money != null && $user_money > 0) {
            echo 'check money';
            $user_money_database = $user->money;
            $user_money_database += $ammount;
            $user->money = $user_money_database;
            $user->save();
        }
        // вставляем историю
        try {
            DB::table('money_history')->insert([
                'date' => $date,
                'user_email' => $user->email,
                'received' => $ammount,
                'operation_id' => $operation_id,
            ]);
        } catch (IOException $exceptione) {
        }

        return response('OK', 200);
    }

    public function getpricestotop(Request $request)
    {

        $toTop = DB::select('select price from prices where price_name = :price_name', ['price_name' => 'to_top']);
        $updatemainimage = DB::select('select price from prices where price_name = :price_name',
            ['price_name' => 'update_main_image']);
        $toFirstPlase = DB::select('select price from prices where price_name = :price_name',
            ['price_name' => 'to_first_plase']);

        return response()->json([
            ["totop" => $toTop],
            ["tofirstplace" => $toFirstPlase],
        ]);
    }

    public function toFirstPlase(Request $request)
    {
        $user = Auth::user();
        $girl = Girl::select([

            'id',
            'user_id',
            'biginvip',
            'endvip',
        ])->where('user_id', $user->id)->first();
        $money = $user->money;
        $toFirstPlase = collect(DB::select('select price from prices where price_name = :price_name',
            ['price_name' => 'to_first_plase']))->first();
        $toFirstPlase->price;
        if ($toFirstPlase->price > $money) {
            return response('lowMoney');
        }
        $current_date = Carbon::now();// текушая дата
        $girl->created_at = $current_date;
        $girl->save();
        $money = $money - $toFirstPlase->price;
        $user->money = $money;
        $user->save();

        return response('ok');
    }

    public function totop(Request $request)
    {
        $user = Auth::user();
        $girl = Girl::select([
            'id',
            'user_id',
            'biginvip',
            'endvip',
        ])->where('user_id', $user->id)->first();

        $money = $user->money;
        $toTop = collect(DB::select('select price from prices where price_name = :price_name',
            ['price_name' => 'to_top']))->first();

        $days = $request->days;
        $days = floor($days);

        if ($toTop->price * $days > $money) {
            return response('lowMoney');
        }
        $current_date = Carbon::now();// текушая дата
        $end_vip = $user->endvip;
        $days = $request->days;
        $days = floor($days); //кругляем полученнаы дни
        //

        if ($end_vip == null or $end_vip < $current_date) {  //есть ли сейчас vip статусэ
            echo "endvip";
            $end_vip = $current_date;
            $begin_vip = $current_date;
            $end_vip = $this->addDayswithdate($end_vip, $days);
        } else {
            $begin_vip = $user->beginvip;
            $end_vip = $this->addDayswithdate($end_vip, $days);
        }


        $user->endvip = $end_vip;
        $user->beginvip = $begin_vip;
        $user->save();

        $new_money = $user->money - $toTop->price * $days;
        DB::table('users')->where('id', $user->id)->update(['money' => $new_money]);

        return response('top');
    }

    //добавляет дни к дате
    private function addDayswithdate($date, $days)
    {
        $date = strtotime("+".$days." days", strtotime($date));

        return date("Y-m-d H:i:s", $date);
    }


}
