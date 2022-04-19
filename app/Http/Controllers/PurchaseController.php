<?php

namespace App\Http\Controllers;

use App\Events\OrderSentToPurchase;
use App\Http\Requests\PurchaseRequest;
use App\Http\Resources\PurchaseResource;
use App\Models\Order;
use App\Models\Purchase;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return PurchaseResource::collection(Purchase::with('orders')->latest()->paginate(1000));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\PurchaseRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PurchaseRequest $request)
    {

        $fields = $request->validated();

        $orders_ids = $fields['orders'];
        $delegate_id = $fields['delegate_id'];

        $orders = Order::whereIn('id', $orders_ids)->get();

        // Count total purchase cost
        // The cost is the result of multiplication of each order product price with order quantity
        $total_cost = 0;

        foreach ($orders as $order) {

            // We don't count products that have is_paid=true
            if ($order['is_paid']) {
                continue;
            }

            $total_cost += $order['total_amount'];
        }

        $purchase = Purchase::create([
            'total_cost' => $total_cost
        ]);

        $purchase_orders = [];

        foreach ($orders_ids as $order_id) {

            $purchase_orders[] = [
                'order_id' => $order_id,
                'delegate_id' => $delegate_id
            ];
        }

        $purchase->orders()->createMany($purchase_orders);

        // Update order status
        $order = new Order();
        $order->whereIn('id', $orders_ids)->whereRaw('status is null')->update(['status' => 'sent_purchase']);

        return response()->json($purchase);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function show(Purchase $purchase)
    {
        return response()->json($purchase::with('orders')->get());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\PurchaseRequest  $request
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function update(PurchaseRequest $request, Purchase $purchase)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function destroy(Purchase $purchase)
    {
        if ($purchase->delete()) {
            return response('', 200);
        } else {
            return response()->json([
                'message' => 'Something went wrong.'
            ], 400);
        }
    }
}
