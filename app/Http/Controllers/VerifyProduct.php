<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class VerifyProduct extends Controller
{
    /**
     * Verify if a product exists and have cost
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {

        $ref = $request->input('ref');

        if (empty($ref)) {
            return response()->json([
                'message' => 'Something went wrong.'
            ], 400);
        }

        $product = Product::where('ref', $ref)->first();

        if (!$product) {
            return response()->json(['message' => 'رقم الصنف غير موجود'], 422);
        } else if ($product->cost == 0) {
            return response()->json(['message' => 'المرجو تحديد تكلفة منتج قبل إضافته'], 422);
        }else if ( $product->is_available == 0) {
            return response()->json(['message' => 'المنتج غير متوفر'], 422);
        }
        
        return response('', 200);
    }
}
