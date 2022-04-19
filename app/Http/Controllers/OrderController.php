<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use App\Models\PurchaseOrder;
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

        return OrderResource::collection(Order::filter($request)->with('product', 'purchase_order')->latest()->orderBy('id', 'desc')->paginate());
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
        $orders_to_merge = [];

        $today_orders = [];
        $today_orders_products = [];

        // If it true, we'll increase the quantity of existing orders that have the same product_id
        $merge_with_today = $request->input('merge_with_today', true);

        if ($merge_with_today) {
            // We merge only the orders that have status null
            $today_orders = Order::whereRaw('date(created_at) = curdate() && status is null')->get(['id', 'product_id', 'quantity'])->toArray();
            $today_orders_products = array_map(function ($array) {
                return $array['product_id'];
            }, $today_orders);
        }

        foreach ($orders as $order) {

            $order_product_id = $order['product_id'];
            $order_product_quantity = $order['quantity'];
            $order_product_cost = $order['product_cost'];
            $order_product_is_paid = $order['is_paid'];
            $order_product_total_amount = $order_product_is_paid ? 0 : $order['product_cost'] * $order['quantity'];

            // if the product_id already exists in a today orders, we'll not create it
            // instead we'll store the product id with the additional quantity to updated later
            if (in_array($order_product_id, $today_orders_products)) {

                if (isset($orders_to_merge[$order_product_id])) {
                    $orders_to_merge[$order_product_id]['addiontal_quantity'] += $order_product_quantity;
                } else {
                    $orders_to_merge[$order_product_id] = [
                        'product_id' => $order_product_id,
                        'addiontal_quantity' => $order_product_quantity
                    ];
                }

                continue;
            }

            // check if the product already added, if true we increase it quantity
            // otherwise, we insert new order to the array
            if (isset($processed_orders[$order_product_id])) {
                $processed_orders[$order_product_id]['quantity']  += $order_product_quantity;
                $processed_orders[$order_product_id]['total_amount']  += $order_product_total_amount;
            } else {
                $processed_orders[$order_product_id] = [
                    'product_id' => $order_product_id,
                    'quantity' => $order_product_quantity,
                    'product_cost' =>  $order_product_cost,
                    'is_paid' => $order_product_is_paid,
                    'total_amount' => $order_product_total_amount
                ];
            }
        }

        foreach ($today_orders as $today_order) {

            if (!isset($orders_to_merge[$today_order['product_id']])) {
                continue;
            }

            // Assign order id to each product, if exists
            $orders_to_merge[$today_order['product_id']]['order_id'] = $today_order['id'];
        }

        $created_orders_ids = [];
        $merged_orders_ids = [];

        foreach ($processed_orders as $processed_order) {
            $new_order = Order::create($processed_order);
            if ($new_order) {
                $created_orders_ids[] = $new_order->id;
            }
        }

        foreach ($orders_to_merge as $order_to_merge) {
            $current_order = Order::find($order_to_merge['order_id']);
            $current_order->quantity += $order_to_merge['addiontal_quantity'];

            $current_order->total_amount += $order_to_merge['addiontal_quantity'] * $current_order->product_cost;

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

        if (empty($status)) {
            return response()->json([
                'message' => 'Something went wrong.'
            ], 400);
        }

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
}
