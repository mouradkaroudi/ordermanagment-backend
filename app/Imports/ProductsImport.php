<?php

namespace App\Imports;

use App\Models\File;
use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithUpserts;

class ProductsImport implements ToModel, WithUpserts
{

    function __construct() {
        $this->current_row = 0;
    }

    /**
     * @return string|array
     */
    public function uniqueBy()
    {
        return 'ref';
    }

    public function model(array $row)
    {

        if($this->current_row > 0 && $row[4]) {

            $image = $row[5];

            $to_be_imported = [
                'sku' => $row[0],
                'ref' => $row[1],
                'cost' => $row[2],
                'name' => $row[4]
            ];

            if($image) {
                
                $file = File::firstOrCreate([
                    'storage_type' => 'cdn',
                    'resource' => $image,
                    'display_name' => $row[4]
                ]);

                $to_be_imported['image_id'] = $file->id;

            }

            return new Product($to_be_imported);
        }

        $this->current_row++;
    }
}
