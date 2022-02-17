<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::insert([
            "ref" => "h4257-1",
            "name" => "دمية Beanie Baby Hello Kitty",
            "sku" => "N11008131A",
            "category_id" => 1,
            "cost" => 10        
        ]);
    }
}
