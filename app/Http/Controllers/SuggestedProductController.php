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
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(SuggestedProduct::class, 'suggested_product');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $request = request();

        $query = SuggestedProduct::filter($request->all());

        // if a user only can add suggested products, we need to show the products that he suggested only
        if($request->user()->tokenCan('add:suggested-products') && !$request->user()->tokenCan('manage:suggested-products')) {
            $query->where('user_id', $request->user()->id);
        }

        return (new SuggestedProductCollection($query->latest()->paginate()));
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

        $data['cost'] = $fields['cost'];
        $data['is_new'] = $request->input('is_new', false);

        $data['store_id'] = $request->input('store_id');
        $data['sell_price'] = $request->input('sell_price');
        $data['sku'] = $request->input('sku');

        $product = SuggestedProduct::create($data);

        return response()->json($product);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SuggestedProduct  $suggested_product
     * @return \Illuminate\Http\Response
     */
    public function show(SuggestedProduct $suggested_product)
    {
        return new SuggestedProductResource($suggested_product);
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

        $data['cost'] = $fields['cost'];
        $data['is_new'] = $request->input('is_new', false);
        $data['store_id'] = $request->input('store_id');
        $data['sell_price'] = $request->input('sell_price');
        $data['sku'] = $request->input('sku');


        if ($suggestedProduct->update($data)) {
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
