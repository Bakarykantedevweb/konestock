<?php

namespace App\Http\Middleware;

use App\Models\User_activity_log;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;


class Historique
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
        $response = $next($request);
            if (Auth::check() && str_contains(Route::current()->getAction()['controller'],"@")) {
                
                $user_id = Auth::id();
                $controller = Route::current()->getAction()['controller'];
                list($controller, $action) = explode('@', $controller);
                $history = new User_activity_log();
                $history->user_id = $user_id;
                $history->controller = $controller;
                $history->action = $action;
                $history->save();
            }

        return $response;
    }
}
