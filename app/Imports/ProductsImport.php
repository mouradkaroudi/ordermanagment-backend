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

            $entries = [
                'sku' => $row[0],
                'ref' => trim(strtolower($row[1])),
                'cost' => $row[2],
                'name' => $row[4]
            ];

            if($image) {
                
                $file = File::firstOrCreate([
                    'storage_type' => 'cdn',
                    'resource' => $image,
                    'display_name' => $row[4]
                ]);

                $entries['image_id'] = $file->id;

            }

            $product = Product::withTrashed()->where('ref', trim(strtolower($row[1])))->orWhere('sku', $row[0])->get()[0];
            
            if(!empty($product)) {
                Product::where('id', $product->id)->update([
                    'cost' => $row[2],
                    'name' => $row[4]
                ]);
            }else{
                $product = Product::create($entries);
            }

        }

        $this->current_row++;
    }
}
