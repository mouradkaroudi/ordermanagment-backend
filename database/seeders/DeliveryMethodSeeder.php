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
        DeliveryMethod::create([
            'name' => 'Fedx',
            'commission' => 5,
            'min' => 1,
            'max' => 5
        ]);
    }
}
