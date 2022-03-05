<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Imports\ProductsImport;
use App\Models\File;
use App\Models\Product;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $request = request()->all();

        return (new ProductCollection(Product::filter($request)->latest()->paginate(50)));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        // Return the valided fields only
        $fields = $request->validated();
        
        // TODO : Move this to another controller
        $file_id = isset($fields['file_id']) ? $fields['file_id']: false;

        if($file_id) {
            $file = File::where('id', $file_id)->first();
            $file_path = $file['resource'];

            try {
                Excel::import(new ProductsImport, $file_path);

                return response()->json([
                    'message' => 'Products imported successfully.'
                ], 200);        
                
            } catch (\Throwable $th) {
                return response()->json([
                    'message' => 'Something went wrong.'
                ], 400);        
            }

        }

        $product = Product::create($fields);
        
        if($product) {
            return response()->json($product);
        }

        return response()->json([
            'message' => 'Something went wrong.'
        ], 400);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, Product $product)
    {

        $fields = $request->validated();

        if($product->update($fields)) {
            return response('', 200);
        }

        return response()->json([
            'message' => 'Something went wrong.'
        ], 400);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        if($product->delete()) {
            return response('', 200);
        }else{
            return response()->json([
                'message' => 'Something went wrong.'
            ], 400);    
        }
    }
}
