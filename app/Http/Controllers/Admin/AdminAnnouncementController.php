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

        // manual build array for data
        $announcementData = [];
        $announcementData['title'] = $validated['title'];
        $announcementData['image_path'] = $imagePath;
        if (isset($validated['admin_name'])) {
            $announcementData['admin_name'] = $validated['admin_name'];
        } else {
            $announcementData['admin_name'] = 'Admin';
        }
        $announcementData['description'] = $validated['description'];
        $announcementData['date_posted'] = $validated['date_posted'];
        $announcementData['deadline'] = $validated['deadline'];

        \App\Models\Announcement::create($announcementData);

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

        // manual build update array
        $updateData = [];
        $updateData['title'] = $validated['title'];

        if (isset($validated['admin_name'])) {
            $updateData['admin_name'] = $validated['admin_name'];
        } else {
            $updateData['admin_name'] = 'Admin';
        }

        $updateData['description'] = $validated['description'];
        $updateData['date_posted'] = $validated['date_posted'];
        $updateData['deadline'] = $validated['deadline'];

        // handle image update explicitly
        if ($request->hasFile('image')) {
            // 1. check and delete old image
            if ($announcement->image_path) {
                $oldPath = public_path($announcement->image_path);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            // 2. upload new image
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/announcements'), $filename);

            // 3. add to update data
            $updateData['image_path'] = 'images/announcements/' . $filename;
        }

        $announcement->update($updateData);

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement updated successfully.');
    }

    public function destroy($id)
    {
        $announcement = \App\Models\Announcement::findOrFail($id);

        // explicitly check and delete image
        if ($announcement->image_path) {
            $fullPath = public_path($announcement->image_path);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }

        $announcement->delete();

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement deleted successfully.');
    }
}
