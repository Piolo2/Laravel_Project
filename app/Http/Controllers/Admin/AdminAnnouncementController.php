<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminAnnouncementController extends Controller
{
    public function index()
    {
        $announcements = \App\Models\Announcement::latest()->get();
        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'admin_name' => 'nullable|string|max:255',
            'description' => 'required|string',
            'date_posted' => 'required|date',
            'deadline' => 'required|date|after_or_equal:date_posted',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/announcements'), $filename);
            $imagePath = 'images/announcements/' . $filename;
        }

        \App\Models\Announcement::create([
            'title' => $validated['title'],
            'image_path' => $imagePath,
            'admin_name' => $validated['admin_name'] ?? 'Admin',
            'description' => $validated['description'],
            'date_posted' => $validated['date_posted'],
            'deadline' => $validated['deadline'],
        ]);

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement created successfully.');
    }

    public function edit($id)
    {
        $announcement = \App\Models\Announcement::findOrFail($id);
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, $id)
    {
        $announcement = \App\Models\Announcement::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'admin_name' => 'nullable|string|max:255',
            'description' => 'required|string',
            'date_posted' => 'required|date',
            'deadline' => 'required|date|after_or_equal:date_posted',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($announcement->image_path && file_exists(public_path($announcement->image_path))) {
                unlink(public_path($announcement->image_path));
            }

            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/announcements'), $filename);
            $announcement->image_path = 'images/announcements/' . $filename;
        }

        $announcement->update([
            'title' => $validated['title'],
            'admin_name' => $validated['admin_name'] ?? 'Admin',
            'description' => $validated['description'],
            'date_posted' => $validated['date_posted'],
            'deadline' => $validated['deadline'],
        ]);

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement updated successfully.');
    }

    public function destroy($id)
    {
        $announcement = \App\Models\Announcement::findOrFail($id);
        if ($announcement->image_path && file_exists(public_path($announcement->image_path))) {
            unlink(public_path($announcement->image_path));
        }
        $announcement->delete();

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement deleted successfully.');
    }
}
