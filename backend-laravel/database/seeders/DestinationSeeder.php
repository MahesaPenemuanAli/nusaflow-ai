<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\DestinationCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DestinationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = DestinationCategory::pluck('id', 'slug');

        $destinations = [
            [
                'category_slug' => 'pantai',
                'name' => 'Pantai Merdeka',
                'description' => 'Pantai berpasir putih dengan pemandangan sunset yang indah. Cocok untuk keluarga dan wisatawan yang ingin bersantai.',
                'address' => 'Jl. Pantai Merdeka No. 1, Kabupaten Pesisir',
                'latitude' => -8.72340000,
                'longitude' => 115.17250000,
                'max_capacity' => 5000,
                'opening_hour' => '06:00',
                'closing_hour' => '18:00',
                'ticket_price' => 15000,
            ],
            [
                'category_slug' => 'pantai',
                'name' => 'Teluk Biru',
                'description' => 'Teluk tersembunyi dengan air biru jernih, ideal untuk snorkeling dan diving.',
                'address' => 'Desa Teluk Biru, Kecamatan Laut Selatan',
                'latitude' => -8.41220000,
                'longitude' => 114.35280000,
                'max_capacity' => 2000,
                'opening_hour' => '07:00',
                'closing_hour' => '17:00',
                'ticket_price' => 25000,
            ],
            [
                'category_slug' => 'budaya',
                'name' => 'Taman Budaya Nusantara',
                'description' => 'Pusat kesenian dan kebudayaan dengan pertunjukan tari, musik, dan pameran seni.',
                'address' => 'Jl. Kebudayaan No. 45, Kota Seni',
                'latitude' => -7.79720000,
                'longitude' => 110.36950000,
                'max_capacity' => 3000,
                'opening_hour' => '09:00',
                'closing_hour' => '21:00',
                'ticket_price' => 20000,
            ],
            [
                'category_slug' => 'museum',
                'name' => 'Museum Kota',
                'description' => 'Museum sejarah kota yang menyimpan artefak dan dokumen bersejarah dari era kolonial.',
                'address' => 'Jl. Sejarah No. 10, Kota Lama',
                'latitude' => -6.13150000,
                'longitude' => 106.81440000,
                'max_capacity' => 1500,
                'opening_hour' => '09:00',
                'closing_hour' => '16:00',
                'ticket_price' => 10000,
            ],
            [
                'category_slug' => 'kuliner',
                'name' => 'Kawasan Kuliner Lama',
                'description' => 'Pusat jajanan dan kuliner tradisional dengan aneka makanan khas daerah.',
                'address' => 'Jl. Kuliner Raya No. 8, Kota Lama',
                'latitude' => -6.96830000,
                'longitude' => 110.41960000,
                'max_capacity' => 4000,
                'opening_hour' => '10:00',
                'closing_hour' => '22:00',
                'ticket_price' => 0,
            ],
            [
                'category_slug' => 'alam',
                'name' => 'Desa Wisata Alam',
                'description' => 'Desa wisata dengan pemandangan sawah terasering, air terjun kecil, dan udara segar pegunungan.',
                'address' => 'Desa Hijau, Kecamatan Pegunungan',
                'latitude' => -7.60830000,
                'longitude' => 110.20360000,
                'max_capacity' => 2500,
                'opening_hour' => '06:00',
                'closing_hour' => '17:00',
                'ticket_price' => 10000,
            ],
            [
                'category_slug' => 'alam',
                'name' => 'Bukit Panorama',
                'description' => 'Puncak bukit dengan panorama 360 derajat. Spot terbaik untuk melihat matahari terbit.',
                'address' => 'Jl. Puncak Bukit KM 12, Kabupaten Tinggi',
                'latitude' => -7.45670000,
                'longitude' => 110.43210000,
                'max_capacity' => 1000,
                'opening_hour' => '05:00',
                'closing_hour' => '18:00',
                'ticket_price' => 20000,
            ],
            [
                'category_slug' => 'religi',
                'name' => 'Masjid Agung Nusantara',
                'description' => 'Masjid bersejarah dengan arsitektur megah perpaduan gaya tradisional dan modern.',
                'address' => 'Jl. Masjid Raya No. 1, Pusat Kota',
                'latitude' => -6.17530000,
                'longitude' => 106.82720000,
                'max_capacity' => 8000,
                'opening_hour' => '04:00',
                'closing_hour' => '22:00',
                'ticket_price' => 0,
            ],
            [
                'category_slug' => 'museum',
                'name' => 'Galeri Seni Rupa',
                'description' => 'Galeri seni kontemporer yang menampilkan karya seniman lokal dan internasional.',
                'address' => 'Jl. Galeri No. 22, Kawasan Seni',
                'latitude' => -6.22250000,
                'longitude' => 106.84690000,
                'max_capacity' => 800,
                'opening_hour' => '10:00',
                'closing_hour' => '18:00',
                'ticket_price' => 30000,
            ],
            [
                'category_slug' => 'budaya',
                'name' => 'Kampung Adat Warisan',
                'description' => 'Kampung adat yang masih mempertahankan tradisi dan arsitektur rumah tradisional.',
                'address' => 'Desa Adat, Kecamatan Warisan Budaya',
                'latitude' => -7.30120000,
                'longitude' => 112.73520000,
                'max_capacity' => 1500,
                'opening_hour' => '08:00',
                'closing_hour' => '17:00',
                'ticket_price' => 15000,
            ],
        ];

        foreach ($destinations as $dest) {
            $categorySlug = $dest['category_slug'];
            unset($dest['category_slug']);

            Destination::updateOrCreate(
                ['slug' => Str::slug($dest['name'])],
                array_merge($dest, [
                    'destination_category_id' => $categories[$categorySlug] ?? null,
                    'slug' => Str::slug($dest['name']),
                    'is_active' => true,
                ])
            );
        }
    }
}
