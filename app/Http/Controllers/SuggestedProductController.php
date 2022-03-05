<?php

namespace App\Http\Controllers;

use App\Http\Requests\SuggestedProductRequest;
use App\Http\Resources\SuggestedProductCollection;
use App\Http\Resources\SuggestedProductResource;
use App\Models\SuggestedProduct;
use Illuminate\Http\Request;

class SuggestedProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $request = request()->all();

        return (new SuggestedProductCollection(SuggestedProduct::filter($request)->latest()->paginate()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SuggestedProductRequest $request)
    {

        $fields = $request->validated();

        $data = [
            'image_id' => $fields['image_id'],
            'category_id' => $fields['category_id'],
            'delivery_method_id' => $fields['delivery_method_id'],
            'user_id' => $request->user()->id,
        ];

        if ($request->input('is_local', false)) {
            $data['cost'] = $fields['cost'];
            $data['is_new'] = $request->input('is_new', false);
        } else {
            $data['store_id'] = $fields['store_id'];
            $data['sell_price'] = $fields['sell_price'];
            $data['sku'] = $fields['sku'];
        }

        $product = SuggestedProduct::create($data);

        return response()->json($product);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SuggestedProduct  $suggestedProduct
     * @return \Illuminate\Http\Response
     */
    public function show(SuggestedProduct $suggestedProduct)
    {
        return new SuggestedProductResource($suggestedProduct);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SuggestedProduct  $suggestedProduct
     * @return \Illuminate\Http\Response
     */
    public function update(SuggestedProductRequest $request, SuggestedProduct $suggestedProduct)
    {

        $fields = $request->validated();

        $data = [
            'image_id' => $fields['image_id'],
            'category_id' => $fields['category_id'],
            'delivery_method_id' => $fields['delivery_method_id'],
            'user_id' => $request->user()->id,
        ];

        if ($request->input('is_local', false)) {
            $data['cost'] = $fields['cost'];
            $data['is_new'] = $request->input('is_new', false);
        } else {
            $data['store_id'] = $fields['store_id'];
            $data['sell_price'] = $fields['sell_price'];
            $data['sku'] = $fields['sku'];
        }

        if($suggestedProduct->update($fields)) {
            return response('', 200);
        }

        return response()->json([
            'message' => 'Something went wrong.'
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SuggestedProduct  $suggestedProduct
     * @return \Illuminate\Http\Response
     */
    public function destroy(SuggestedProduct $suggestedProduct)
    {
        if ($suggestedProduct->delete()) {
            return response('', 200);
        } else {
            return response()->json([
                'message' => 'Something went wrong.'
            ], 400);
        }
    }
}
