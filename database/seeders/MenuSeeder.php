<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define the menus to be seeded
        $menus = [
            ['title' => 'Dashboard',],
            ['title' => 'Bank Santri'],
            ['title' => 'Rekening'],
            ['title' => 'Transaksi',],
            ['title' => 'Manjemen Santri',],
            ['title' => 'Santri',],
            ['title' => 'Pendaftaran',],
            ['title' => 'Wali Santri',],
            ['title' => 'Manajemen Staf',],
            ['title' => 'Staf',],
            ['title' => 'Hak Akses',],
            ['title' => 'Peran',],
        ];

        // Insert each menu into the database
        foreach ($menus as $index => $menu) {
            Menu::create([
                'title' => $menu['title'],
                'description' => $menu['description'] ?? null,
                'icon' => $menu['icon'] ?? null,
                'route' => $menu['route'] ?? null,
                'parent_id' => $menu['parent_id'] ?? null,
                'type' => $menu['type'] ?? 'main',
                'position' => $menu['position'] ?? 'sidebar',
                'status' => $menu['status'] ?? 'active',
                'order' => $menu['order'] ?? $index + 1,
            ]);
        }
    }
}
