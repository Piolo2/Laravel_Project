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

        // fetch raw skills data
        $my_skills_data = $user->skills()->with('category')->withPivot('id', 'availability_status', 'description')->get();

        // Manual mapping
        $my_skills = [];
        foreach ($my_skills_data as $skill) {
            $skill->pivot_id = $skill->pivot->id;
            $skill->skill_name = $skill->name;
            $skill->availability_status = $skill->pivot->availability_status;

            if ($skill->category) {
                $skill->category_name = $skill->category->name;
            } else {
                $skill->category_name = 'Uncategorized';
            }

            $skill->description = $skill->pivot->description;
            $my_skills[] = $skill;
        }

        // Fetch all skills for the dropdown
        $all_skills = \App\Models\Skill::with('category')->get();

        // Manual grouping by category
        $skills_by_category = [];
        foreach ($all_skills as $skill) {
            $catName = 'Uncategorized';
            if ($skill->category) {
                $catName = $skill->category->name;
            }

            if (!isset($skills_by_category[$catName])) {
                $skills_by_category[$catName] = [];
            }
            $skills_by_category[$catName][] = $skill;
        }

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
        $already_exists = $user->skills()->where('skill_id', $request->skill_id)->exists();
        if ($already_exists) {
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

        // 2. Perform Update
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
