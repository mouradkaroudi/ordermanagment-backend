<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchasePutRequest;
use App\Http\Requests\PurchaseRequest;
use App\Http\Resources\PurchaseResource;
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

        return PurchaseResource::collection(Purchase::filter($request)->latest()->paginate(1000));
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

        $delegate_id =  $request->user()->id;

        $purchase = Purchase::create([
            'order_id' => $order_id,
            'delegate_id' => $delegate_id,
            'quantity' => $quantity
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
        $recieved_quantity = $request->input('quantity');

        $quantity = $purchase->quantity;

        $missing_quantity = $quantity - $recieved_quantity;

        $status = $missing_quantity == 0 ? 'completed' : 'missing_quantity';
        $purchase->update([
            'order_id' => $order_id,
            'quantity' => $quantity,
            'status' => $status,
            'missing_quantity' => $missing_quantity,
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
