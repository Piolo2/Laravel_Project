<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use App\Models\SkillCategory;
use App\Models\UserSkill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkillController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $my_skills = $user->skills()->with('category')->withPivot('id', 'availability_status', 'description')->get()
            ->map(function ($skill) {
                $skill->pivot_id = $skill->pivot->id;
                $skill->skill_name = $skill->name;
                $skill->availability_status = $skill->pivot->availability_status;
                $skill->category_name = $skill->category->name ?? 'Uncategorized';
                $skill->description = $skill->pivot->description;
                return $skill;
            });

        $skills_by_category = \App\Models\Skill::with('category')->get()->groupBy(function ($skill) {
            return $skill->category->name ?? 'Uncategorized';
        });

        return view('my_skills', compact('my_skills', 'skills_by_category'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'skill_id' => 'required|exists:skills,id',
            'description' => 'nullable|string'
        ]);

        $user = Auth::user();

        // Check if skill already added
        if ($user->skills()->where('skill_id', $request->skill_id)->exists()) {
            return back()->with('error', 'You have already added this skill.');
        }

        $user->skills()->attach($request->skill_id, [
            'description' => $request->description,
            'availability_status' => 'Available'
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Skill added successfully!']);
        }

        return back()->with('msg', 'Skill added successfully!');
    }

    public function toggle($id, $status)
    {
        $userId = Auth::id();
        \Illuminate\Support\Facades\Log::info("Toggle Status Request", ['id' => $id, 'user_id' => $userId, 'status' => $status]);

        // 1. Check if the record exists and belongs to the user
        $exists = \Illuminate\Support\Facades\DB::table('user_skills')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->exists();

        if (!$exists) {
            \Illuminate\Support\Facades\Log::warning("Toggle Failed: Record not found or access denied", ['id' => $id, 'user_id' => $userId]);
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Service not found or access denied.']);
            }
            return back()->with('error', 'Service not found or access denied.');
        }

        // 2. Perform Update (Ignore result count, as setting same value returns 0)
        \Illuminate\Support\Facades\DB::table('user_skills')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->update(['availability_status' => $status]);

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => "Status updated to $status!"]);
        }
        return back()->with('msg', "Status updated to $status!");
    }

    public function destroy($id)
    {
        $userSkill = UserSkill::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        $userSkill->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Skill removed from your profile.']);
        }

        return back()->with('msg', 'Skill removed from your profile.');
    }
}
