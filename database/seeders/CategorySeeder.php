<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::insert([
            [
                'name' => 'الالعاب',
                'commission' => 10
            ],
            [
                'name' => 'الخردوات والهوم',
                'commission' => 10
            ],
            [
                'name' => 'الساعات',
                'commission' => 14
            ],
            [
                'name' => 'اكسسوارات جوال',
                'commission' => 9
            ]
        ]);
    }
}
