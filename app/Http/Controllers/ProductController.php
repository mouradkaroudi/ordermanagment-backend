<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Imports\ProductsImport;
use App\Models\File;
use App\Models\Product;
use App\Models\ProductSuppliers;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{

    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Product::class, 'product');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $request = request()->all();
        $per_page = $request['per_page'] ?? 50;

        $query = Product::filter($request)->latest();

        if ($per_page == -1) {
            $query = $query->get();
        } else {
            $query = $query->paginate($per_page);
        }

        return (new ProductCollection($query));
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
        $file_id = isset($fields['file_id']) ? $fields['file_id'] : false;

        if ($file_id) {
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

        // convert ref to lowercase
        $fields['ref'] = strtolower($fields['ref']);

        $product = Product::create($fields);

        if ($product) {
            if(isset($fields['suppliers'])) {
                $product->suppliers()->createMany($fields['suppliers']);
            }
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

        //convert ref to lowercase
        $fields['ref'] = trim(strtolower($fields['ref']));

        if ($product->update($fields)) {
            if (isset($fields['suppliers']) && is_array($fields['suppliers'])) {
                foreach( $fields['suppliers'] as $supplier ) {
                    $arr = [
                        'supplier_id' => $supplier['supplier_id'],
                        'product_id' => $product->id,
                    ];
                    $product->suppliers()->updateOrCreate($arr, $arr);
                }
            }
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
        if ($product->delete()) {
            return response('', 200);
        } else {
            return response()->json([
                'message' => 'Something went wrong.'
            ], 400);
        }
    }
}
