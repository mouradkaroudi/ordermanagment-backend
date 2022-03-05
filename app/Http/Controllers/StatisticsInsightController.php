<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\SuggestedProduct;
use Illuminate\Http\Request;

class StatisticsInsightController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $unsent_orders = Order::whereRaw('status is null')->count();
        $orders_with_problems = Order::where('status', 'issue')->count();
        $suggested_products = SuggestedProduct::count();

        $total_orders = Order::count();
        $orders_purchased = Order::where('status', 'purchased')->count();

        $purchased_orders_percent = $total_orders == 0 ? '-' : ($orders_purchased / $total_orders) * 100;

        return response()->json([
            'unsent_orders' => $unsent_orders,
            'orders_with_problems' => $orders_with_problems,
            'suggested_products' => $suggested_products,
            'purchased_orders_percent' => round($purchased_orders_percent,0) . '%'
        ]);

    }

}
