<?php

namespace Database\Seeders;

use App\Models\Categories;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ['Action', 'Drama', 'Comedy', 'Sci-Fi', 'Fantasy', 'Thriller', 'Horror'];

        foreach ($categories as $name) {
            Categories::create([
                'slug' => Str::slug($name),
                'name' => $name,
            ]);
        }
    }
}
