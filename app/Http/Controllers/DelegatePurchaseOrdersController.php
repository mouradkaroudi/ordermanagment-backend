<?php

namespace App\Http\Controllers;

use App\Http\Resources\DelegatePurchaseOrderResource;
use App\Models\Order;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class DelegatePurchaseOrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $user_id = request()->user()->id;

        $query = PurchaseOrder::with('order')->where('delegate_id', $user_id)->latest()->orderBy('id', 'desc');

        return DelegatePurchaseOrderResource::collection($query->paginate());

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $user_id = $request->user()->id;

        if(PurchaseOrder::where(['order_id' => $id, 'delegate_id' => $user_id])->count() == 0) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $status = $request->input('status');
        //return response()->json($status, 400);


        if($status == 'issue' || $status == 'purchased') {
            return Order::where('id', $id)->update([
                'status' => $status
            ]);
        }

        return response()->json('Oops!', 400);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
