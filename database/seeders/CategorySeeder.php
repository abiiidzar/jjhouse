<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Atasan', 'slug' => 'atasan'],
            ['name' => 'Bawahan', 'slug' => 'bawahan'],
            ['name' => 'Outer', 'slug' => 'outer'],
            ['name' => 'Aksesoris', 'slug' => 'aksesoris'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
