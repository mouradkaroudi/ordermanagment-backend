<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchasePutRequest;
use App\Http\Requests\PurchaseRequest;
use App\Http\Resources\PurchaseResource;
use App\Models\Order;
use App\Models\Purchase;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{

    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Purchase::class, 'purchase');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $request = request()->all();

        $per_page = $request['per_page'] ?? 50;

        $query = Purchase::filter($request)->latest();

        if ($per_page == -1) {
            $query = $query->get();
        } else {
            $query = $query->paginate($per_page);
        }

        return PurchaseResource::collection($query);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\PurchaseRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PurchaseRequest $request)
    {

        $request->validated();

        $order_id =  $request->input('order_id');
        $quantity =  $request->input('quantity');
        $is_from_warehouse = $request->input('is_from_warehouse', false);
        $delegate_id =  $request->user()->id;

        // get quantity from orders table
        $order = Order::find($order_id);
        $ordered_quantity = $order->products->sum('quantity');

        if ($quantity > $ordered_quantity) {
            return response('', 400);
        }

        $purchase = Purchase::create([
            'order_id' => $order_id,
            'delegate_id' => $delegate_id,
            'quantity' => $quantity,
            'is_from_warehouse' => $is_from_warehouse
        ]);

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
        return new PurchaseResource($purchase);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\PurchaseRequest  $request
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function update(PurchasePutRequest $request, Purchase $purchase)
    {
        $request->validated();

        $order_id = $request->input('order_id');
        $inventory_quantity = $request->input('quantity');

        $status = 'completed';

        if($inventory_quantity > $purchase->quantity) {
            $status = 'excessive_amount';
        }else if($inventory_quantity < $purchase->quantity) {
            $status = 'missing_quantity';
        }

        $purchase->update([
            'order_id' => $order_id,
            //'quantity' => $quantity,
            'status' => $status,
            'inventory_quantity' => $inventory_quantity,
            'reviewier_id' => request()->user()->id
        ]);

        return response()->json([], 200);
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

    //
    // Custom endpoints
    //


    /**
     * 
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id)
    {

        //$this->authorize('invoice', $request);

        $invoice_id = $request->input('invoice_id');

        $purchase = Purchase::find($id);

        $purchase->update([
            'status' => 'completed',
            'return_invoice_id' => $invoice_id
        ]);

        return response()->json([], 200);
    }
}
