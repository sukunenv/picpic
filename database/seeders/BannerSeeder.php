<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        Banner::create([
            'title' => 'Buy 1 Get 1 Free*',
            'description' => 'Berlaku untuk semua jenis kopi espresso.',
            'button_text' => 'Ambil Promo',
            'button_url' => '/menu',
            'image' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&q=80&w=600',
            'is_active' => true,
            'order' => 1,
        ]);

        Banner::create([
            'title' => 'Menu Baru: Matcha Latte',
            'description' => 'Cobain varian green tea terbaru kami yang segar dan creamy!',
            'button_text' => 'Lihat Menu',
            'button_url' => '/menu',
            'image' => 'https://images.unsplash.com/photo-1515823662972-da6a2e4d3002?auto=format&fit=crop&q=80&w=600',
            'is_active' => true,
            'order' => 2,
        ]);
    }
}
