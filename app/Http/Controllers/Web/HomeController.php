<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SiteSettings;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $settings = SiteSettings::all();
        return view('home.index', [
            'settings'=>$settings
        ]);
    }
}
