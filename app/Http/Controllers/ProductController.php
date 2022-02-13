<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductCollection;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return (new ProductCollection(Product::latest()->paginate()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'ref' => ['required'],
            'name' => ['required'],
            'sku' => ['required'],
            'mainRef' => ['required'],
            'supplier_id' => ['required'],
            'location_id' => ['required'],
            'category_id' => ['required'],
            'cost' => ['required'],
            'is_paid' => ['required'],
        ]);

        $product = Product::create([
            'ref' => $request->input('ref'),
            'name' => $request->input('name'),
            'image_id' => $request->input('image_id'),
            'sku' => $request->input('sku'),
            'mainRef' => $request->input('mainRef'),
            'supplier_id' => $request->input('supplier_id'),
            'location_id' => $request->input('location_id'),
            'category_id' => $request->input('category_id'),
            'cost' => $request->input('cost'),
            'is_paid' => $request->input('is_paid'),
        ]);

        return response()->json($product);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
}
