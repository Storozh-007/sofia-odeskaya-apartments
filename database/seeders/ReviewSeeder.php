<?php

namespace Database\Seeders;

use App\Models\Apartment;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $apartments = Apartment::all();

        if ($users->isEmpty() || $apartments->isEmpty()) return;

        foreach ($apartments as $apartment) {
            // Create 3-5 reviews per apartment
            $count = rand(3, 5);
            
            for ($i = 0; $i < $count; $i++) {
                Review::create([
                    'user_id' => $users->random()->id,
                    'apartment_id' => $apartment->id,
                    'rating' => rand(4, 5), // Mostly good reviews
                    'comment' => $this->getRandomComment(),
                ]);
            }
        }
    }

    private function getRandomComment(): string
    {
        $comments = [
            'Absolutely stunning views! The service was impeccable.',
            'A true gem in the heart of the city. Highly recommended.',
            'Clean, modern, and very comfortable. Will definitely stay again.',
            'The host was very accommodating and the location is perfect.',
            'Luxury at its finest. Worth every penny.',
            'Best stay I have had in years. The details make the difference.',
        ];

        return $comments[array_rand($comments)];
    }
}
