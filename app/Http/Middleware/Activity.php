<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User_activity_log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class Activity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $path= $request->path();
        $activity = new User_activity_log();
        $user = Auth::logout();
        $slash=substr($path,0);

            if(isset($user))
            {
                $data = [
                    'user_id'=>$user,
                    'modify_user'=>$path
                ];
            }elseif($slash=="/")
            {
                $data = [
                    'user_id'=>$user,
                    'modify_user'=>'Page Accueil'
                ];
            }
            else
            {
                $data =['modify_user'=>"Utilisateur Inconnu $path"];
            }
            $activity->create($data);
        return $next($request);


    }
}
