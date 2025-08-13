<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $r){
        $data = [
            'pageTitle' => "Dashboard"
        ];

        return view('dashboard.index', $data);
    }
}
