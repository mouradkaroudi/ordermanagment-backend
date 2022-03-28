<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderTrackingCollection;
use App\Models\Order;
use App\Models\OrderTracking;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Order $order)
    {   
        
        return (new OrderTrackingCollection(OrderTracking::where('order_id', $order->id)->latest()->get()));
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
            'order_id' => ['required', 'exists:App\Models\Order,id'],
            'message' => ['required']
        ]);

        $orderTrack = OrderTracking::create([
            'order_id' => $request->input('order_id'),
            'user_id' => $request->user()->id,
            'type' => 'note',
            'message' => $request->input('message'),
        ]);
        
        return response()->json($orderTrack);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OrderTracking  $orderTracking
     * @return \Illuminate\Http\Response
     */
    public function show(OrderTracking $orderTracking)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\OrderTracking  $orderTracking
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, OrderTracking $orderTracking)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OrderTracking  $orderTracking
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrderTracking $orderTracking)
    {
        //
    }
}
