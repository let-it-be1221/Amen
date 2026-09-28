<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index()
    {
        $tables = RestaurantTable::latest()->paginate(15);
        return view('admin.tables.index', compact('tables'));
    }

    public function create()
    {
        return view('admin.tables.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:tables,name'],
            'seats' => ['required', 'integer', 'min:1', 'max:50'],
            'location' => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        RestaurantTable::create($validated);

        return redirect()
            ->route('admin.tables.index')
            ->with('success', "Table {$validated['name']} created.");
    }

    public function edit(RestaurantTable $table)
    {
        return view('admin.tables.edit', compact('table'));
    }

    public function update(Request $request, RestaurantTable $table)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:tables,name,' . $table->id],
            'seats' => ['required', 'integer', 'min:1', 'max:50'],
            'location' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:available,occupied,reserved'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', false);

        $table->update($validated);

        return redirect()
            ->route('admin.tables.index')
            ->with('success', "Table {$table->name} updated.");
    }

    public function destroy(RestaurantTable $table)
    {
        $table->delete();
        return redirect()
            ->route('admin.tables.index')
            ->with('success', "Table {$table->name} deleted.");
    }
}
