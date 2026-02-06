<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProviderLandingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $profile = $user->profile;

        $viewData = [];
        $viewData['user'] = $user;
        $viewData['profile'] = $profile;

        return view('service_provider', $viewData);
    }
}
