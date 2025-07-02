<?php

namespace Database\Seeders;

use App\Models\Films;
use App\Models\Reviews;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $films = Films::all();

        foreach ($films as $film) {
            foreach ($users->random(rand(1, 3)) as $user) {
                Reviews::create([
                    'film_id' => $film->id,
                    'user_id' => $user->id,
                    'comment' => 'Review for film ' . $film->id,
                    'mark' => rand(1, 10),
                ]);
            }
        }
    }
}
