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
        $announcements = \App\Models\Announcement::active()
            ->orderBy('date_posted', 'desc')
            ->get();
        return view('index', compact('announcements'));
    }

    public function showAnnouncement($id)
    {
        $announcement = \App\Models\Announcement::findOrFail($id);
        return view('announcements.show', compact('announcement'));
    }
}
