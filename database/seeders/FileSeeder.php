<?php

namespace Database\Seeders;

use App\Models\Files;
use App\Models\Films;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $series = Films::where('type', 'serial')->get();
        $films = Films::where('type', 'film')->get();
        foreach ($films as $film) {
            Files::create([
                'film_id' => $film->id,
                'season_number' => null,
                'episode_number' => null,
                'link' => 'https://example.com/' . $film->id,
            ]);
        }
        foreach ($series as $film) {
            for ($season = 1; $season <= rand(1, 3); $season++) {
                for ($episode = 1; $episode <= rand(3, 6); $episode++) {
                    Files::create([
                        'film_id' => $film->id,
                        'season_number' => $season,
                        'episode_number' => $episode,
                        'link' => 'https://example.com/' . $film->id . "/s{$season}e{$episode}",
                    ]);
                }
            }
        }

    }
}
