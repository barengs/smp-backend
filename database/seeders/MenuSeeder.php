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
            ['name' => 'Dashboard',],
            ['name' => 'Asatidz'],
            ['name' => 'Santri'],
            ['name' => 'Users',],
            ['name' => 'Roles',],
            ['name' => 'Permissions',],
            ['name' => 'Settings',],
            ['name' => 'Reports',],
            ['name' => 'Logs',],
            ['name' => 'Notifications',],
            ['name' => 'Profile',],
            ['name' => 'Help',],
        ];

        // Insert each menu into the database
        foreach ($menus as $index => $menu) {
            Menu::creaate([
                'name' => $menu['name'],
                'description' => $menu['description'] ?? null,
                'icon' => $menu['icon'] ?? null,
                'route' => $menu['route'] ?? null,
                'parent_id' => $menu['parent_id'] ?? null,
                'type' => $menu['type'] ?? 'link',
                'position' => $menu['position'] ?? 'side',
                'status' => $menu['status'] ?? 'active',
                'order' => $menu['order'] ?? $index + 1,
            ]);
        }
    }
}
