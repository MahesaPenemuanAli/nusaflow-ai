<?php

namespace Database\Seeders;

use App\Models\DestinationCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DestinationCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Pantai',
                'description' => 'Destinasi wisata pantai dan pesisir laut.',
                'icon' => 'beach',
            ],
            [
                'name' => 'Budaya',
                'description' => 'Destinasi wisata budaya, adat, dan tradisi.',
                'icon' => 'theater',
            ],
            [
                'name' => 'Museum',
                'description' => 'Museum dan galeri seni.',
                'icon' => 'museum',
            ],
            [
                'name' => 'Kuliner',
                'description' => 'Kawasan wisata kuliner dan makanan khas.',
                'icon' => 'restaurant',
            ],
            [
                'name' => 'Alam',
                'description' => 'Destinasi wisata alam, gunung, dan hutan.',
                'icon' => 'forest',
            ],
            [
                'name' => 'Religi',
                'description' => 'Destinasi wisata religi dan tempat ibadah bersejarah.',
                'icon' => 'temple',
            ],
        ];

        foreach ($categories as $category) {
            DestinationCategory::updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'slug' => Str::slug($category['name']),
                    'description' => $category['description'],
                    'icon' => $category['icon'],
                    'is_active' => true,
                ]
            );
        }
    }
}
