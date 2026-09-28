<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('menuItems')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'icon' => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $request->integer('sort_order', 0);

        Category::create($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', "Category {$validated['name']} created.");
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'icon' => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', false);
        $validated['sort_order'] = $request->integer('sort_order', 0);

        $category->update($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', "Category {$category->name} updated.");
    }

    public function destroy(Category $category)
    {
        if ($category->menuItems()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete a category that has menu items. Move or delete them first.']);
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', "Category {$category->name} deleted.");
    }
}
