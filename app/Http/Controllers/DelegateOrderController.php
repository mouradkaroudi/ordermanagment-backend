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
        $request = request()->all();
        
        $query = Order::where('delegate_id', $user_id)->filter($request)->whereIn('status',['sent', 'uncompleted_quantity'])->orderBy('updated_at', 'desc');

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
