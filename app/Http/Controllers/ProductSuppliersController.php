<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductSuppliersCollection;
use App\Models\ProductSuppliers;
use Illuminate\Http\Request;

class ProductSuppliersController extends Controller
{
    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->authorizeResource(ProductSuppliers::class, 'product');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new ProductSuppliersCollection((ProductSuppliers::latest()->paginate()));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($product_id, $id)
    {

        $supplier = ProductSuppliers::where('id', $id);

        if ($supplier->delete()) {
            return response('', 200);
        } else {
            return response()->json([
                'message' => 'Something went wrong.'
            ], 400);
        }
    }
}
