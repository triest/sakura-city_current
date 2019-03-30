<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Comment;

use Response;
use Symfony\Component\Filesystem\Exception\IOException;

use Auth;

use App\Repositories\ImageRepository;

use App\User;



use settype;
use Hash;


class CustomUserController extends Controller
{
    //
    function index()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->phone_conferd == 1) {
                return redirect('anket');
            } else {
                return view('custom.resetSMS');
            }
        }
        return view('custom.registration');
    }
    public function join(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'you' => 'required',
            'kogo' => 'required',
        ]);
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
       /* $user->you = $request->you;
        $user->kogo = $request->kogo;*/
        $user->password = bcrypt($request->password);
        $user->save();
        Auth::loginUsingId($user->id);
        return view('custom.resetSMS2');
    }
}
