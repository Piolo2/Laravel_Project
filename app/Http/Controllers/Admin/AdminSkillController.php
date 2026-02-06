<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use App\Models\SkillCategory;
use Illuminate\Http\Request;

class AdminSkillController extends Controller
{
    public function index()
    {
        $skills = Skill::with('category')->paginate(15);
        $categories = SkillCategory::all();
        return view('admin.skills.index', compact('skills', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:skill_categories,id',
        ]);

        // Explicitly build data array
        $skillData = [];
        $skillData['name'] = $validated['name'];
        $skillData['category_id'] = $validated['category_id'];

        Skill::create($skillData);

        return redirect()->route('admin.skills.index')->with('success', 'Skill created successfully.');
    }

    public function update(Request $request, $id)
    {
        $skill = Skill::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:skill_categories,id',
        ]);

        // Explicitly build update data
        $updateData = [];
        $updateData['name'] = $validated['name'];
        $updateData['category_id'] = $validated['category_id'];

        $skill->update($updateData);

        return redirect()->route('admin.skills.index')->with('success', 'Skill updated successfully.');
    }

    public function destroy($id)
    {
        $skill = Skill::findOrFail($id);
        $skill->delete();

        return redirect()->route('admin.skills.index')->with('success', 'Skill deleted successfully.');
    }

    // Category Management methods could also go here or in a separate controller
    public function storeCategory(Request $request)
    {
        $validated = $request->validate(['name' => 'required|string|max:255|unique:skill_categories']);

        $categoryData = [];
        $categoryData['name'] = $validated['name'];

        SkillCategory::create($categoryData);

        return redirect()->back()->with('success', 'Category created successfully.');
    }
}
