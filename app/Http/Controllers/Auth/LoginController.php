<?php

namespace App\Http\Controllers\Auth;

use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    //use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
        ]);
        $users = DB::table('users')->get();
        $name    = $request->name;
        $password = $request->password;
        $user = $users->where('name', $name)->first();
        if (!empty($user)) {
            if (Hash::check($password, $user->password)) {
                Auth::attempt(['name' => $name, 'password' => $password]);
                $activityLog = [
                    'user_id'       => $user->id,
                ];
                $session = Session::create($activityLog);
                session()->put('session', $session->id);

                return redirect('/admin/dashboard')->with('message', 'Bienvenu sur Kone Stock');
            }

            return redirect('login')->with('error', 'name ou Mot de passe incorrect', 'Erreur: ');
        }
        return redirect('login')->with('error', 'name ou Mot de passe incorrect', 'Erreur:');
    }

    public function logout()
    {
        $activityLog = [
                'deconnection'  => now(),
            ];
        Session::find(session('session'))->update(['deconnection' => $activityLog['deconnection']]);
        Auth::logout();
        return redirect('login')->with('message', 'Merci pour votre visite et à bientôt');
    }
}
