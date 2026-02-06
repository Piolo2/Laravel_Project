<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application landing page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $query = \App\Models\Announcement::active();
        $query->orderBy('date_posted', 'desc');
        $announcements = $query->get();

        $data = [];
        $data['announcements'] = $announcements;

        return view('index', $data);
    }

    public function showAnnouncement($id)
    {
        $announcement = \App\Models\Announcement::findOrFail($id);

        $data = [];
        $data['announcement'] = $announcement;

        return view('announcements.show', $data);
    }
}
