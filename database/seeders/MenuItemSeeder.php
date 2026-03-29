<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // ── KOPI ──────────────────────────────────────────────
            [
                'name'         => 'Espresso',
                'category'     => 'Kopi',
                'price'        => 18000,
                'description'  => 'Shot espresso murni yang kuat dan aromatic.',
                'image'        => 'https://images.unsplash.com/photo-1510591509098-f4fdc6d0ff04?w=400',
                'is_available' => true,
                'is_popular'   => true,
                'rating'       => 4.8,
            ],
            [
                'name'         => 'Cappuccino',
                'category'     => 'Kopi',
                'price'        => 28000,
                'description'  => 'Perpaduan sempurna antara espresso, susu steam, dan foam lembut.',
                'image'        => 'https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=400',
                'is_available' => true,
                'is_popular'   => true,
                'rating'       => 4.9,
            ],
            [
                'name'         => 'Americano',
                'category'     => 'Kopi',
                'price'        => 22000,
                'description'  => 'Espresso shot dengan tambahan air panas untuk rasa yang lebih ringan.',
                'image'        => 'https://images.unsplash.com/photo-1551030173-122aabc4489c?w=400',
                'is_available' => true,
                'is_popular'   => false,
                'rating'       => 4.5,
            ],
            [
                'name'         => 'Kopi Susu',
                'category'     => 'Kopi',
                'price'        => 25000,
                'description'  => 'Kopi susu khas Picpic dengan gula aren organik.',
                'image'        => 'https://images.unsplash.com/photo-1561882468-9110e03e0f78?w=400',
                'is_available' => true,
                'is_popular'   => true,
                'rating'       => 4.7,
            ],

            // ── NON-KOPI ──────────────────────────────────────────
            [
                'name'         => 'Matcha Latte',
                'category'     => 'Non-Kopi',
                'price'        => 30000,
                'description'  => 'Teh hijau Jepang premium dipadukan dengan susu segar creamy.',
                'image'        => 'https://images.unsplash.com/photo-1515823064-d6e0c04616a7?w=400',
                'is_available' => true,
                'is_popular'   => true,
                'rating'       => 4.7,
            ],
            [
                'name'         => 'Coklat Panas',
                'category'     => 'Non-Kopi',
                'price'        => 27000,
                'description'  => 'Coklat Belgia murni yang dilelehkan dengan susu panas.',
                'image'        => 'https://images.unsplash.com/photo-1542990253-0d0f5be5f0ed?w=400',
                'is_available' => true,
                'is_popular'   => false,
                'rating'       => 4.6,
            ],

            // ── MAKANAN ───────────────────────────────────────────
            [
                'name'         => 'Nasi Goreng',
                'category'     => 'Makanan',
                'price'        => 35000,
                'description'  => 'Nasi goreng rempah tradisional dengan telur dan ayam.',
                'image'        => 'https://images.unsplash.com/photo-1512058564366-18510be2db19?w=400',
                'is_available' => true,
                'is_popular'   => true,
                'rating'       => 4.8,
            ],
            [
                'name'         => 'Sandwich',
                'category'     => 'Makanan',
                'price'        => 32000,
                'description'  => 'Roti gandum isi tuna, selada segar, dan keju melted.',
                'image'        => 'https://images.unsplash.com/photo-1528735602780-2552fd46c7af?w=400',
                'is_available' => true,
                'is_popular'   => false,
                'rating'       => 4.3,
            ],
            [
                'name'         => 'Roti Bakar',
                'category'     => 'Makanan',
                'price'        => 25000,
                'description'  => 'Roti bakar coklat keju dengan topping melimpah.',
                'image'        => 'https://images.unsplash.com/photo-1484723091739-30a097e8f929?w=400',
                'is_available' => true,
                'is_popular'   => true,
                'rating'       => 4.5,
            ],

            // ── DESSERT ───────────────────────────────────────────
            [
                'name'         => 'Tiramisu',
                'category'     => 'Dessert',
                'price'        => 38000,
                'description'  => 'Lapisan krim mascarpone lembut dengan aroma kopi espresso.',
                'image'        => 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?w=400',
                'is_available' => true,
                'is_popular'   => true,
                'rating'       => 5.0,
            ],
            [
                'name'         => 'Pancake',
                'category'     => 'Dessert',
                'price'        => 30000,
                'description'  => 'Pancake lembut dengan sirup maple dan buah berry segar.',
                'image'        => 'https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?w=400',
                'is_available' => true,
                'is_popular'   => false,
                'rating'       => 4.6,
            ],
            [
                'name'         => 'Cheesecake',
                'category'     => 'Dessert',
                'price'        => 42000,
                'description'  => 'New York style cheesecake yang padat dan creamy.',
                'image'        => 'https://images.unsplash.com/photo-1533134242443-d4fd215305ad?w=400',
                'is_available' => true,
                'is_popular'   => true,
                'rating'       => 4.9,
            ],
        ];

        foreach ($items as $item) {
            MenuItem::updateOrCreate(['name' => $item['name']], $item);
        }

        $this->command->info('✅ 12 menu items updated with Unsplash URLs!');
    }
}
