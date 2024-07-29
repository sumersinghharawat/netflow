<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('languages')->delete();
        $data = [
            [
                'code' => 'en',
                'name' => 'English',
                'name_in_english' => 'english',
                'flag_image' => 'us.jpg',
                'status' => 1,
                'default' => 1,
            ],
            [
                'code' => 'es',
                'name' => 'Español',
                'name_in_english' => 'spanish',
                'flag_image' => 'spain.jpg',
                'status' => 0,
                'default' => 0,
            ],
            [
                'code' => 'ch',
                'name' => '中文',
                'name_in_english' => 'chinese',
                'status' => 0,
                'flag_image' => 'ch.png',
                'default' => 0,

            ],
            [
                'code' => 'de',
                'name' => 'Deutsch',
                'name_in_english' => 'german',
                'flag_image' => 'germany.jpg',
                'status' => 0,
                'default' => 0,

            ],
            [
                'code' => 'pt',
                'name' => 'Português',
                'name_in_english' => 'portuguese',
                'flag_image' => 'pt.png',
                'status' => 0,
                'default' => 0,

            ],
            [
                'code' => 'fr',
                'name' => 'français',
                'name_in_english' => 'french',
                'flag_image' => 'fr.png',
                'status' => 0,
                'default' => 0,

            ],
            [
                'code' => 'it',
                'name' => 'italiano',
                'name_in_english' => 'italian',
                'flag_image' => 'italy.jpg',
                'status' => 0,
                'default' => 0,

            ],
            [
                'code' => 'tr',
                'name' => 'Türk',
                'name_in_english' => 'turkish',
                'flag_image' => 'tr.png',
                'status' => 0,
                'default' => 0,

            ],
            [
                'code' => 'po',
                'name' => 'polski',
                'name_in_english' => 'polish',
                'flag_image' => 'po.png',
                'status' => 0,
                'default' => 0,

            ],
            [
                'code' => 'ar',
                'name' => 'العربية',
                'name_in_english' => 'arabic',
                'flag_image' => 'ar.png',
                'status' => 0,
                'default' => 0,

            ],
            [
                'code' => 'ru',
                'name' => 'русский',
                'name_in_english' => 'russian',
                'flag_image' => 'russia.jpg',
                'status' => 0,
                'default' => 0,

            ],

        ];

        Language::insert($data);
    }
}
