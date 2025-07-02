<?php

namespace Database\Seeders;

use App\Models\Categories;
use App\Models\Films;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FilmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 20; $i++) {
            $film = Films::create([
                'poster' => 'posters/poster' . $i . '.jpg',
                'title' => 'Sample Film ' . $i,
                'description' => 'Description for film ' . $i,
                'type' => $i <= 10 ? 'film' : 'serial',
                'release_date' => now()->subYears(rand(1, 10)),
                'isVisible' => true,
            ]);

            // Привязка случайных категорий
            $film->category()->attach(
                Categories::inRandomOrder()->take(rand(1, 3))->pluck('id')
            );
        }
    }

}
