<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Location::insert(
            [
                [
                    'name' => 'باب مكة',
                ],
                [
                    'name' => 'الخاسكية',
                ],
                [
                    'name' => 'الهنداوية',
                ],
                [
                    'name' => 'الجنوبية'
                ]
            ]
        );
    }
}
