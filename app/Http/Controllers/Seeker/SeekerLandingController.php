<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use App\Models\SkillCategory;
use Illuminate\Http\Request;

class SeekerLandingController extends Controller
{
    public function index()
    {
        $top_cats = SkillCategory::take(3)->get();

        $viewData = [];
        $viewData['top_cats'] = $top_cats;

        return view('service_seeker', $viewData);
    }
}
