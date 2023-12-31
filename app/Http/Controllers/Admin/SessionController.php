<?php

namespace App\Http\Controllers\Admin;

use App\Models\Session;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SessionController extends Controller
{
    public function index()
    {
        $activityLog = Session::paginate(10);
        return view('admin.sessionAdmin.index', compact('activityLog'));
    }
}
