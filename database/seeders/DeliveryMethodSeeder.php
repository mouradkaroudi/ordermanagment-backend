<?php

namespace Database\Seeders;

use App\Models\DeliveryMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeliveryMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DeliveryMethod::insert([
            [
                'name' => 'FBN',
                'commission' => 6,
                'min' => 9,
                'max' => 3
            ],
            [
                'name' => 'Direct ship',
                'commission' => 8,
                'min' => 12,
                'max' => 3
            ]
        ]);
    }
}
