<?php

namespace App\Http\Controllers;

use App\Http\Resources\DelegateOrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class DelegateOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_id = request()->user()->id;

        $query = Order::where('delegate_id', $user_id)->whereIn('status',['sent', 'uncompleted_quantity'])->latest()->orderBy('id', 'desc');

        return DelegateOrderResource::collection($query->get());

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
    
}
