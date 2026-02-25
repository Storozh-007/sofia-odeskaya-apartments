<?php

namespace Database\Seeders;

use App\Models\Apartment;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class TargetedReviewSeeder extends Seeder
{
    public function run(): void
    {
        // Find or create the user
        $user = User::firstOrCreate(
            ['email' => 'anna.brush@example.com'],
            [
                'name' => 'Anna Brushnevskaya',
                'password' => bcrypt('password'),
                'role' => 'guest',
            ]
        );

        // Find the "Odesa Pearl" apartment (or the first one if not found)
        $apartment = Apartment::where('title', 'like', '%Odesa%')->first() ?? Apartment::first();

        if ($apartment) {
            Review::create([
                'user_id' => $user->id,
                'apartment_id' => $apartment->id,
                'rating' => 5,
                'comment' => 'Девочки, это просто разрыв! 🔥 Отдыхали с подругой, всё на высшем уровне. Вид на море — закачаешься, интерьер — пушка, фотки получились нереальные! Хозяин душка, всё чисто, стильно, модно. Обязательно вернемся ещё! 10/10 💖',
                'image_url' => '/images/anna.jpg',
            ]);
        }
    }
}
