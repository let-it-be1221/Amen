<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MenuItemController extends Controller
{
    public function index(Request $request)
    {
        $query = MenuItem::with('category');

        if ($categoryId = $request->get('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($search = $request->get('q')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->has('available')) {
            $query->where('is_available', $request->boolean('available'));
        }

        $menuItems = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.menu-items.index', compact('menuItems', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('admin.menu-items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999.99'],
            'preparation_time' => ['nullable', 'integer', 'min:0', 'max:600'],
            'is_available' => ['boolean'],
            'is_vegetarian' => ['boolean'],
            'is_spicy' => ['boolean'],
            'calories' => ['nullable', 'string', 'max:30'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,webp,jpg', 'max:2048'],
        ]);

        $validated['is_available'] = $request->boolean('is_available', true);
        $validated['is_vegetarian'] = $request->boolean('is_vegetarian', false);
        $validated['is_spicy'] = $request->boolean('is_spicy', false);
        $validated['preparation_time'] = $request->integer('preparation_time', 15);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menu-items', 'public');
            $validated['image'] = $path;
        }

        MenuItem::create($validated);

        // Bump menu version - the QR code shown to waiters will be regenerated
        \App\Models\SystemSetting::set('menu_version', (string) \Illuminate\Support\Str::uuid(), 'menu');

        return redirect()
            ->route('admin.menu-items.index')
            ->with('success', "Menu item {$validated['name']} created.");
    }

    public function edit(MenuItem $menuItem)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('admin.menu-items.edit', compact('menuItem', 'categories'));
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999.99'],
            'preparation_time' => ['nullable', 'integer', 'min:0', 'max:600'],
            'is_available' => ['boolean'],
            'is_vegetarian' => ['boolean'],
            'is_spicy' => ['boolean'],
            'calories' => ['nullable', 'string', 'max:30'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,webp,jpg', 'max:2048'],
        ]);

        $validated['is_available'] = $request->boolean('is_available', false);
        $validated['is_vegetarian'] = $request->boolean('is_vegetarian', false);
        $validated['is_spicy'] = $request->boolean('is_spicy', false);
        $validated['preparation_time'] = $request->integer('preparation_time', 15);

        if ($request->hasFile('image')) {
            if ($menuItem->image) {
                Storage::disk('public')->delete($menuItem->image);
            }
            $validated['image'] = $request->file('image')->store('menu-items', 'public');
        }

        $menuItem->update($validated);

        // Bump menu version
        \App\Models\SystemSetting::set('menu_version', (string) \Illuminate\Support\Str::uuid(), 'menu');

        return redirect()
            ->route('admin.menu-items.index')
            ->with('success', "Menu item {$menuItem->name} updated.");
    }

    public function destroy(MenuItem $menuItem)
    {
        if ($menuItem->image) {
            Storage::disk('public')->delete($menuItem->image);
        }
        $menuItem->delete();

        // Bump menu version
        \App\Models\SystemSetting::set('menu_version', (string) \Illuminate\Support\Str::uuid(), 'menu');

        return redirect()
            ->route('admin.menu-items.index')
            ->with('success', "Menu item {$menuItem->name} deleted.");
    }

    public function toggleAvailability(MenuItem $menuItem)
    {
        $menuItem->update(['is_available' => !$menuItem->is_available]);

        // Bump menu version - the QR code (which encodes the version) will change
        \App\Models\SystemSetting::set('menu_version', (string) \Illuminate\Support\Str::uuid(), 'menu');

        return back()->with('success', "Menu item {$menuItem->name} is now " . ($menuItem->is_available ? 'available' : 'unavailable') . ".");
    }
}
