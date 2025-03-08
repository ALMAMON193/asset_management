<?php

namespace App\Http\Controllers\Web\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(){
        if(!Auth::check()){
            return view('web.backend.layout.auth.login');
        }else{
            return redirect()->route('admin.dashboard');
        }
    }

    public function register(){
        return view('web.backend.layout.auth.register');
    }
}
