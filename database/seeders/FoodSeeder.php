<?php

namespace Database\Seeders;

use App\Models\Food;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FoodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $foods = [
            [
                'name' => 'Cake',
                'menu_id' => 1,
                'description' => 'Lorem ipsum dolor, sit amet consectetur adipisicing elit. Suscipit, dicta quibusdam quos laborum neque quod.',
                'image_url' => '/storage/foods/cake.jpg',
                'price' => 15000,
                'is_available' => true
            ],
            [
                'name' => 'IceCream',
                'menu_id' => 1,
                'description' => 'Lorem ipsum dolor, sit amet consectetur adipisicing elit. Suscipit, dicta quibusdam quos laborum neque quod.',
                'image_url' => '/storage/foods/ice-cream.jpg',
                'price' => 15000,
                'is_available' => true
            ],
            [
                'name' => 'Pizza',
                'menu_id' => 2,
                'description' => 'Tasty pizza with cheese, tomato, and pepperoni.',
                'image_url' => '/storage/foods/pizza.jfif',
                'price' => 25000,
                'is_available' => true
            ],
            [
                'name' => 'Chilipizza',
                'menu_id' => 2,
                'description' => 'Tasty pizza with cheese, tomato, and pepperoni.',
                'image_url' => '/storage/foods/pizza-2.jpg',
                'price' => 25000,
                'is_available' => true
            ],
            [
                'name' => 'Burger',
                'menu_id' => 3,
                'description' => 'Juicy burger with lettuce, tomato, and cheese.',
                'image_url' => '/storage/foods/burger.jfif',
                'price' => 20000,
                'is_available' => true
            ],
            [
                'name' => 'CheesBurger',
                'menu_id' => 3,
                'description' => 'Juicy burger with lettuce, tomato, and cheese.',
                'image_url' => '/storage/foods/burger.jfif',
                'price' => 20000,
                'is_available' => true
            ],
        ];
        Food::insert($foods);
    }
}
