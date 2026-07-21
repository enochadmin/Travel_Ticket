<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitiesSeeder extends Seeder
{
    /**
     * Major Ethiopian cities and regional capitals (CSA / regional administrative references).
     */
    public function run(): void
    {
        $cities = [
            // Chartered cities
            ['name' => 'Addis Ababa', 'region' => 'Addis Ababa'],
            ['name' => 'Dire Dawa', 'region' => 'Dire Dawa'],

            // Afar
            ['name' => 'Semera', 'region' => 'Afar'],
            ['name' => 'Asayita', 'region' => 'Afar'],
            ['name' => 'Dubti', 'region' => 'Afar'],
            ['name' => 'Awash', 'region' => 'Afar'],

            // Amhara
            ['name' => 'Bahir Dar', 'region' => 'Amhara'],
            ['name' => 'Gondar', 'region' => 'Amhara'],
            ['name' => 'Dessie', 'region' => 'Amhara'],
            ['name' => 'Debre Markos', 'region' => 'Amhara'],
            ['name' => 'Debre Birhan', 'region' => 'Amhara'],
            ['name' => 'Kombolcha', 'region' => 'Amhara'],
            ['name' => 'Woldiya', 'region' => 'Amhara'],
            ['name' => 'Lalibela', 'region' => 'Amhara'],
            ['name' => 'Debre Tabor', 'region' => 'Amhara'],
            ['name' => 'Debre Sina', 'region' => 'Amhara'],
            ['name' => 'Finote Selam', 'region' => 'Amhara'],
            ['name' => 'Kemise', 'region' => 'Amhara'],

            // Benishangul-Gumuz
            ['name' => 'Asosa', 'region' => 'Benishangul-Gumuz'],
            ['name' => 'Pawe', 'region' => 'Benishangul-Gumuz'],

            // Central Ethiopia
            ['name' => 'Hosaena', 'region' => 'Central Ethiopia'],
            ['name' => 'Welkite', 'region' => 'Central Ethiopia'],
            ['name' => 'Butajira', 'region' => 'Central Ethiopia'],

            // Gambela
            ['name' => 'Gambela', 'region' => 'Gambela'],
            ['name' => 'Itang', 'region' => 'Gambela'],

            // Harari
            ['name' => 'Harar', 'region' => 'Harari'],

            // Oromia
            ['name' => 'Adama', 'region' => 'Oromia'],
            ['name' => 'Jimma', 'region' => 'Oromia'],
            ['name' => 'Bishoftu', 'region' => 'Oromia'],
            ['name' => 'Nekemte', 'region' => 'Oromia'],
            ['name' => 'Shashamane', 'region' => 'Oromia'],
            ['name' => 'Ambo', 'region' => 'Oromia'],
            ['name' => 'Asella', 'region' => 'Oromia'],
            ['name' => 'Robe', 'region' => 'Oromia'],
            ['name' => 'Goba', 'region' => 'Oromia'],
            ['name' => 'Dembidolo', 'region' => 'Oromia'],
            ['name' => 'Waliso', 'region' => 'Oromia'],
            ['name' => 'Burayu', 'region' => 'Oromia'],
            ['name' => 'Sebeta', 'region' => 'Oromia'],
            ['name' => 'Ziway', 'region' => 'Oromia'],
            ['name' => 'Bule Hora', 'region' => 'Oromia'],
            ['name' => 'Gimbi', 'region' => 'Oromia'],
            ['name' => 'Metu', 'region' => 'Oromia'],
            ['name' => 'Ginir', 'region' => 'Oromia'],

            // Sidama
            ['name' => 'Hawassa', 'region' => 'Sidama'],
            ['name' => 'Yirgalem', 'region' => 'Sidama'],
            ['name' => 'Dilla', 'region' => 'Sidama'],
            ['name' => 'Aleta Wendo', 'region' => 'Sidama'],

            // Somali
            ['name' => 'Jijiga', 'region' => 'Somali'],
            ['name' => 'Gode', 'region' => 'Somali'],
            ['name' => 'Degehabur', 'region' => 'Somali'],
            ['name' => 'Kebri Dahar', 'region' => 'Somali'],

            // South Ethiopia
            ['name' => 'Arba Minch', 'region' => 'South Ethiopia'],
            ['name' => 'Bonga', 'region' => 'South Ethiopia'],
            ['name' => 'Karat', 'region' => 'South Ethiopia'],
            ['name' => 'Konso', 'region' => 'South Ethiopia'],
            ['name' => 'Wolaita Sodo', 'region' => 'South Ethiopia'],
            // Southwest Ethiopia
            ['name' => 'Mizan Teferi', 'region' => 'Southwest Ethiopia'],
            ['name' => 'Tepi', 'region' => 'Southwest Ethiopia'],

            // Tigray
            ['name' => 'Mekelle', 'region' => 'Tigray'],
            ['name' => 'Axum', 'region' => 'Tigray'],
            ['name' => 'Adigrat', 'region' => 'Tigray'],
            ['name' => 'Shire', 'region' => 'Tigray'],
            ['name' => 'Wukro', 'region' => 'Tigray'],
            ['name' => 'Humera', 'region' => 'Tigray'],
        ];

        foreach ($cities as $city) {
            City::updateOrCreate(
                ['name' => $city['name']],
                ['region' => $city['region'], 'is_active' => true]
            );
        }
    }
}
