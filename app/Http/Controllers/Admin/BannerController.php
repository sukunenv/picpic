<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BannerController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Banners', [
            'banners' => Banner::orderBy('order')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string',
            'button_url'  => 'nullable|string',
            'image'       => 'nullable|string',
            'is_active'   => 'boolean',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'order'       => 'integer'
        ]);

        Banner::create($validated);
        return redirect()->back()->with('success', 'Banner berhasil ditambahkan.');
    }

    public function update(Banner $banner, Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string',
            'button_url'  => 'nullable|string',
            'image'       => 'nullable|string',
            'is_active'   => 'boolean',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'order'       => 'integer'
        ]);

        $banner->update($validated);
        return redirect()->back()->with('success', 'Banner berhasil diperbarui.');
    }

    public function toggle(Banner $banner)
    {
        $banner->update(['is_active' => !$banner->is_active]);
        return redirect()->back()->with('success', 'Status banner diperbarui.');
    }

    public function destroy(Banner $banner)
    {
        $banner->delete();
        return redirect()->back()->with('success', 'Banner berhasil dihapus.');
    }
}
