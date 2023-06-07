<?php

namespace App\Http\Controllers\Admin;

use App\Models\School;
use App\Models\Classroom;
use App\Models\StudentClass;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard.dashboard');
    }
}
