<?php

namespace App\Http\Middleware;

use App\Girl;
use Closure;
use Illuminate\Support\Facades\Auth;

class AnketExist
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($user = Auth::user()) {
            $girl = Girl::select([
                'name',
                'id',
                'phone',
                'description',
                'private',
                'main_image',
                'sex',
                'meet',
                'weight',
                'height',
            ])->where('user_id', $user->id)->first();
            if ($girl == null) {
                return redirect('/index');
            }

            return $next($request);
        }

        return redirect("/continion/");

    }
}
