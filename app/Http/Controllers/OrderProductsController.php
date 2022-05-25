<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderProductsResource;
use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Http\Request;

class OrderProductsController extends Controller
{

    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Order::class, 'order');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id)
    {
        return OrderProductsResource::collection(OrderProduct::where('order_id', $id)->paginate());
    }

    public function show(OrderProduct $orderProduct)
    {
        return $orderProduct::get();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OrderProduct  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order, $orderProductId)
    {
        $orderProduct = OrderProduct::find($orderProductId);
        
        if ($orderProduct->delete()) {
            return response('', 200);
        } else {
            return response()->json([
                'message' => 'Something went wrong.'
            ], 400);
        }
    }
}
