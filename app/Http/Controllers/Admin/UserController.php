<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User_activity_log;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index');
    }
    public function activityLog()
    {
        $activityLog = User_activity_log::paginate(10);
        return view('admin.users.user_activity_log', compact('activityLog'));
    }
}
