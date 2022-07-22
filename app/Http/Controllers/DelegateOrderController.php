<?php

namespace App\Http\Controllers;

use App\Http\Resources\DelegateOrderResource;
use App\Models\Order;
use App\Models\Product;
use App\Models\Supplier;

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
        
        $query = Order::filter($request)->where('delegate_id', $user_id)->whereIn('status',['sent', 'uncompleted_quantity'])->orderBy('updated_at', 'desc');
        $query = $query->get();

        return DelegateOrderResource::collection($query);

    }

    /**
     * Display all delegates in orders
     */
    public function suppliers() {
        $user_id = request()->user()->id;
        $request = request()->all();
        
        $orders = Order::filter($request)->where('delegate_id', $user_id)->whereIn('status',['sent', 'uncompleted_quantity'])->get()->toArray();

        $products_ids = array_unique(array_map(function($a) {
            return $a['product_id'];
        }, $orders));

        $products = Product::whereIn('id', $products_ids)->with('suppliers')->get()->toArray();

        $suppliers_ids = [];

        foreach( $products as $product ) {
            $suppliers = $product['suppliers'];
            foreach( $suppliers as $supplier ) {
                if(!in_array($supplier['supplier_id'], $suppliers_ids)) {
                    $suppliers_ids[] = $supplier['supplier_id'];
                }
            }
        }

        $suppliers = Supplier::whereIn('id', $suppliers_ids)->get();
        
        return $suppliers;

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
