<?php

namespace Database\Seeders;

use App\Models\Apartment;
use App\Models\ApartmentImage;
use Illuminate\Database\Seeder;

class ApartmentImageSeeder extends Seeder
{
    public function run(): void
    {
        $apartments = Apartment::all();

        foreach ($apartments as $apartment) {
            // Delete existing images if any
            $apartment->images()->delete();

            // Main image (from apartment record or default)
            $mainImage = $apartment->image_url ?? 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?q=80&w=2070&auto=format&fit=crop';
            
            ApartmentImage::create([
                'apartment_id' => $apartment->id,
                'image_url' => $mainImage,
                'sort_order' => 1,
            ]);

            // Add 4 more random interior/detail shots
            $this->addAdditionalImages($apartment);
        }
    }

    private function addAdditionalImages(Apartment $apartment)
    {
        $images = [
            'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=800&q=80', // Living room
            'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80', // Bedroom
            'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=800&q=80', // Bathroom
            'https://images.unsplash.com/photo-1613490493576-7fde63acd811?auto=format&fit=crop&w=800&q=80', // Kitchen
            'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=800&q=80', // Detail
        ];

        // Shuffle and pick 4
        shuffle($images);
        $selected = array_slice($images, 0, 4);

        foreach ($selected as $index => $url) {
            ApartmentImage::create([
                'apartment_id' => $apartment->id,
                'image_url' => $url,
                'sort_order' => $index + 2,
            ]);
        }
    }
}
