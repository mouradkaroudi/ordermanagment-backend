<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
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
    public function index()
    {
        $request = request()->all();

        return OrderResource::collection(Order::filter($request)->latest()->orderBy('id', 'desc')->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrderRequest $request)
    {

        $fields = $request->validated();

        // Client enter orders by ref
        // We need to assign each ref a product id
        $orders = $fields['orders'];

        $processed_orders = $this->processOrders($orders);

        foreach ($processed_orders as $processed_order) {
            $order = Order::create($processed_order);
            $order->products()->createMany($processed_order['products']);
        }

        return response()->json([]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        return new OrderResource($order);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {

        $status = $request->input('status');
        $orders = $request->input('orders');

        if(!empty($orders)) {
            $processed_orders = $this->processOrders($orders);
            $order->products()->createMany($processed_orders[$order->product_id]['products']);
        }

        if (!empty($status)) {
            $update = $order->update([
                'status' => $status
            ]);

            if(!$update) {
                response()->json([
                    'message' => 'Something went wrong.'
                ], 400);
            }
        }


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        if ($order->delete()) {

            // When delete a order, we need also to delete purchase orders linked to that order
            //PurchaseOrder::where('order_id', $order->id);

            return response('', 200);
        } else {
            return response()->json([
                'message' => 'Something went wrong.'
            ], 400);
        }
    }

    //
    // Custom api endpoints
    //

    /**
     * Update order(s) delegate
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function assignDelegate(Request $request)
    {

        // Validate request
        $request->validate([
            'orders.*' => ['required', 'exists:App\Models\Order,id'],
            'delegate_id' => ['required', 'exists:App\Models\User,id']
        ]);

        // Inputs
        $orders_ids = $request->input('orders');
        $delegate_id = $request->input('delegate_id');

        
        Order::whereIn('id', $orders_ids)->update(['status' => 'sent', 'delegate_id' => $delegate_id]);

        return response()->json([], 200);
    }

    //

    private function processOrders( $orders ) {

        $query_orders = Product::whereIn('ref', array_map(function ($a) {
            return $a['ref'];
        }, $orders))->get();

        $map_orders_by_ref = [];

        foreach ($query_orders as $query_order) {
            $map_orders_by_ref[$query_order['ref']] = $query_order;
        }

        foreach ($orders as $key => $order) {
            $orders[$key]['product_id'] = $map_orders_by_ref[$order['ref']]['id'];
            $orders[$key]['product_cost'] = $map_orders_by_ref[$order['ref']]['cost'];
            $orders[$key]['is_paid'] = $map_orders_by_ref[$order['ref']]['is_paid'];
        }

        $processed_orders = [];

        // Create a unique order for each individual product

        foreach ($orders as $order) {

            $order_product_id = $order['product_id'];
            $order_product_quantity = $order['quantity'];
            $order_product_cost = $order['product_cost'];
            $order_product_is_paid = $order['is_paid'];
            $order_product_store_id = $order['store_id'];
            $order_product_total_amount = $order_product_is_paid ? 0 : $order['product_cost'] * $order['quantity'];

            $processed_orders[$order_product_id]['product_id'] = $order_product_id;
            $processed_orders[$order_product_id]['product_cost'] = $order_product_cost;
            $processed_orders[$order_product_id]['is_paid'] = $order_product_is_paid;

            $processed_orders[$order_product_id]['products'][] = [
                'user_id' => 1,
                'quantity' => $order_product_quantity,
                'store_id' => $order_product_store_id,
                'total_amount' => $order_product_total_amount
            ];
        }

        return $processed_orders;

    }

}
