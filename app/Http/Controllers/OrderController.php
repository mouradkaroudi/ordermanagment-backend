<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return OrderResource::collection(Order::with('product', 'purchase_order')->latest()->paginate());
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
        // We need to assign each ref to a product id
        $orders = $fields['orders'];

        $query_orders = Product::whereIn('ref', array_map(function($a) { return $a['ref']; }, $orders))->get();

        $map_orders_by_ref = [];

        foreach( $query_orders as $query_order ) {
            $map_orders_by_ref[$query_order['ref']] = $query_order;
        }

        /*
        [[
            'ref' => 'h-10000', // change ref by id
            'quantity' => 4
        ]]

        [[
            'product_id' => 1,
            'quantity' => 4
         ]]
        */
            
        foreach( $orders as $key=>$order ) {
            $orders[$key]['product_id'] = $map_orders_by_ref[$order['ref']]['id'];
        }

        $processed_orders = [];
        $orders_to_merge = [];

        $today_orders = [];
        $today_orders_products = [];

        // If it true, we'll increase the quantity of existing orders that have the same product_id
        $merge_with_today = $request->input('merge_with_today', true);

        if($merge_with_today) {
            $today_orders = Order::whereRaw('date(created_at) = curdate() && status is null')->get(['id', 'product_id', 'quantity'])->toArray();
            $today_orders_products = array_map(function( $array ) {
                return $array['product_id'];
            }, $today_orders);    
        }

        foreach( $orders as $order ) {

            $order_product_id = $order['product_id'];
            $order_product_quantity = $order['quantity'];

            // if the product_id already exists in a today orders, we'll not create it
            // instead we'll store the product id with the additional quantity to updated later
            if(in_array($order_product_id, $today_orders_products)) {

                if( isset( $orders_to_merge[$order_product_id] ) ) {
                    $orders_to_merge[$order_product_id]['addiontal_quantity'] += $order_product_quantity;
                }else{
                    $orders_to_merge[$order_product_id] = [
                        'product_id' => $order_product_id,
                        'addiontal_quantity' => $order_product_quantity
                    ];
                }

                continue;

            }
            
            // check if the product already added, if true we increase it quantity
            // otherwise, we insert new order to the array
            if( isset( $processed_orders[$order_product_id] ) ) {
                $processed_orders[$order_product_id]['quantity']  += $order_product_quantity;
            }else{
                $processed_orders[$order_product_id] = [
                    'product_id' => $order_product_id,
                    'quantity' => $order_product_quantity
                ];
            }

        }

        foreach( $today_orders as $today_order ) {
            
            if(!isset( $orders_to_merge[$today_order['product_id']] )) {
                continue;
            }

            // Assign order id to each product, if exists
            $orders_to_merge[$today_order['product_id']]['order_id'] = $today_order['id'];
        }

        $created_orders_ids = [];
        $merged_orders_ids = [];

        foreach( $processed_orders as $processed_order ) {
            $new_order = Order::create($processed_order);
            if($new_order) {
                $created_orders_ids[] = $new_order->id;
            }
        }

        foreach( $orders_to_merge as $order_to_merge ) {
            $current_order = Order::find( $order_to_merge['order_id'] );
            $current_order->quantity += $order_to_merge['addiontal_quantity'];
            $current_order->save();

            $merged_orders_ids[] = $current_order->id;

        }

        return response()->json([
            'created' => $created_orders_ids,
            'merged' => $merged_orders_ids
        ]);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        //
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
        $quantity = $request->input('quantity');
        $notes = $request->input('notes');
        
        $order->update([
            'status' => $status
        ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }
}
