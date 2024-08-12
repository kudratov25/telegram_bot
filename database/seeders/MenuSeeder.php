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
        Menu::create([
            'name' => 'Desserts',
            'is_active' => true,
            'url' => 'desserts'
        ]);
        Menu::create([
            'name' => 'Pizzas',
            'is_active' => true,
            'url' => 'desserts'
        ]);
        Menu::create([
            'name' => 'Burgers',
            'is_active' => true,
            'url' => 'desserts'
        ]);
    }
}
