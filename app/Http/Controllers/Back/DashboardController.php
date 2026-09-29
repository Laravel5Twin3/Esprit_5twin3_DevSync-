<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('back.dashboard', [
            'usersCount' => User::count(),
        ]);
    }
}
