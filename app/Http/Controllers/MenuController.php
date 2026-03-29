<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $query = MenuItem::query()->where('is_available', true);

        if ($request->category && $request->category !== 'Semua') {
            $query->where('category', $request->category);
        }

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        return response()->json([
            'data' => $query->orderByDesc('is_popular')->orderByDesc('rating')->get()
        ]);
    }
}
