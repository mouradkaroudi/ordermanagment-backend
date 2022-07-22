<?php

namespace App\Http\Controllers;

use App\Exports\CsvConvertExport;
use App\Models\File;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File as FacadesFile;
use Maatwebsite\Excel\Facades\Excel;

class ConvertCsvController extends Controller
{
    /**
     * Convert the uploaded csv by calculating the cost (cost / 1.15)
     * and match each ref with main_ref in products table
     * and change ref with main_ref if exists
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        
        $request->validate([
            'file_id' => ['required', 'exists:App\Models\File,id']
        ]);

        $file_id = $request->input('file_id');

        $file = File::where('id', $file_id)->first();

        $file_path = public_path($file['resource']);
        $theArray = Excel::toArray([], $file_path)[0];

        $new_csv = [[
            "Item No",
            "qty",
            "price",
            "item_status",
            "sku",
            "Partner_sku"
        ]];

        for($i=1; $i<=count($theArray); $i++) {

            if(!isset($theArray[$i])) {
                continue;
            }

            $sku = $theArray[$i][3];
            $cost = $theArray[$i][65] / 1.15;
            $itemStatus = $theArray[$i][10];
            $partner_sku = $theArray[$i][1];
            if(!empty($sku)) {
                $product = Product::where('sku', $sku)->first();
                if(!empty($product)) {

                    $ref = isset($product->main_ref) ? $product->main_ref : $product->ref;

                    $new_csv[] = [
                        $ref,
                        1,
                        $cost,
                        $itemStatus,
                        $sku,
                        $partner_sku
                    ];
                } 
            }
        }

        $converted_csv = (new Collection($new_csv))->storeExcel(
            'converted-csv.xlsx',
            'public',
        );

        if($converted_csv) {

            if(FacadesFile::delete($file_path)) {
                File::where('id', $file_id)->delete();
            }

            return response()->json([
                'message' => 'Converted successfully',
                'file_url' => asset('storage/converted-csv.xlsx')
            ], 200);
        }

        return response()->json([
            'message' => 'Convert failed'
        ], 400);

    }
}
