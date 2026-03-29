<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MenuItemController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Menu', [
            'menuItems' => MenuItem::orderBy('category')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'category'     => 'required|string',
            'price'        => 'required|numeric',
            'description'  => 'nullable|string',
            'image'        => 'nullable|string',
            'is_available' => 'boolean',
        ]);

        MenuItem::create($validated);

        return redirect()->back()->with('success', 'Menu berhasil ditambahkan.');
    }

    public function update(MenuItem $menuItem, Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'category'     => 'required|string',
            'price'        => 'required|numeric',
            'description'  => 'nullable|string',
            'image'        => 'nullable|string',
            'is_available' => 'boolean',
        ]);

        $menuItem->update($validated);

        return redirect()->back()->with('success', 'Menu berhasil diperbarui.');
    }

    public function toggle(MenuItem $menuItem)
    {
        $menuItem->update(['is_available' => !$menuItem->is_available]);

        return redirect()->back()->with('success', 'Status ketersediaan diperbarui.');
    }

    public function destroy(MenuItem $menuItem)
    {
        $menuItem->delete();

        return redirect()->back()->with('success', 'Menu berhasil dihapus.');
    }
}
