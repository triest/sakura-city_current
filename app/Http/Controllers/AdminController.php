<?php

namespace App\Http\Controllers;

use App\Events\eventPreasent;
use App\GiftAct;
use App\Girl;
use App\Present;
use App\User;
use Illuminate\Http\Request;
use File;
use App\ImageResize;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class AdminController extends Controller
{
    //
    public function adminPanel()
    {
        return view('admin/adminPanel');
    }


}
