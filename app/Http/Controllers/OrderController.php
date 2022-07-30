<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\File;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\File as FacadesFile;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;

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

    protected function resourceMethodsWithoutModels()
    {
        return array_merge(parent::resourceMethodsWithoutModels(), ['destroyMany', 'updateMany']);
    }

    protected function resourceAbilityMap()
    {
        return array_merge(parent::resourceAbilityMap(), [
            'destroyMany' => 'destroyMany',
            'updateMany' => 'updateMany'
        ]);
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

        $query = Order::filter($request)->orderBy('updated_at', 'desc');

        if ($per_page == -1) {
            $query = $query->get();
        } else {
            $query = $query->paginate($per_page);
        }

        return OrderResource::collection($query);
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

        $processed_orders = $this->processOrders($orders);

        foreach ($processed_orders as $processed_order) {
            $order = Order::create($processed_order);
            $order->products()->createMany($processed_order['products']);
        }

        return response()->json([]);
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
        $orders = $request->input('orders');

        if (!empty($orders)) {
            $processed_orders = $this->processOrders($orders);
            $order->products()->createMany($processed_orders[$order->product_id]['products']);
        }

        if (!empty($status)) {
            $update = $order->update([
                'status' => $status
            ]);

            if (!$update) {
                response()->json([
                    'message' => 'Something went wrong.'
                ], 400);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order, Request $request)
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

    //
    // Custom api endpoints
    //

    public function updateMany( Request $request ) {
        
        $ids = $request->input('ids');
        $status = $request->input('status');

        if(empty($ids) || empty($status)) {
            return response()->json([
                'message' => 'Something went wrong.'
            ], 400);
        }

        $query = Order::whereIn('id', $ids)->update([
            'status' => $status
        ]);

        return response('', 200);

    }

    public function destroyMany(Request $request)
    {

        $ids = $request->input('ids');

        if (empty($ids)) {
            return response()->json([
                'message' => 'Something went wrong.'
            ], 400);
        }

        Order::destroy($ids);

        return response('', 200);
    }

    /**
     * Update order(s) delegate
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function assignDelegate(Request $request)
    {

        // Validate request
        $request->validate([
            'orders.*' => ['required', 'exists:App\Models\Order,id'],
            'delegate_id' => ['required', 'exists:App\Models\User,id']
        ]);

        // Inputs
        $orders_ids = $request->input('orders');
        $delegate_id = $request->input('delegate_id');

        $user = User::where('id', $delegate_id)->first();

        if ($user->fcm_token) {

            $fcm_token = json_decode($user->fcm_token);

            $notification = [
                'notification' => [
                    'title' => 'وصلتك طلبات جديدة من الإدارة',
                ],
                'registration_ids' => [$fcm_token->token]
            ];

            $response = Http::withHeaders([
                'content-type' => 'application/json',
                'authorization' => 'key=AAAAUPHtdJM:APA91bGD6C3VtLauKkxnJrbXnmvbRl13ggPwtLeHyWZPyn3MYOXkWNxtHm-6OSHV1eSQLgYYV9oUCnmzNEBOprrev0OCK6X1zZpT6jvWy1-mAgjNrYQl4SRo3_npo_AyjO3y2N-sYQc9'
            ])->withBody(json_encode($notification), 'application/json')->post('https://fcm.googleapis.com/fcm/send');
        }

        Order::whereIn('id', $orders_ids)->update(['status' => 'sent', 'delegate_id' => $delegate_id]);

        return response()->json([], 200);
    }

    public function import(Request $request)
    {

        $request->validate([
            'file_id' => ['required', 'exists:App\Models\File,id'],
            'store_id' => ['required', 'exists:App\Models\Store,id']
        ]);

        $file_id = $request->input('file_id');
        $store_id = $request->input('store_id');

        $file = File::where('id', $file_id)->first();

        $file_path = public_path($file['resource']);
        $theArray = Excel::toArray([], $file_path)[0];

        $orders = [];

        $invalid_products = [];

        foreach ($theArray as $order) {

            $product = Product::where('ref', $order[0])->first();

            if (!isset($product->id)) {
                $invalid_products[] = [
                    'ref' => $order[0],
                    'quantity' => $order[1],
                    'issue' => 'Product not exist'
                ];
                continue;
            } else if ($product->cost == 0) {
                $invalid_products[] = [
                    'ref' => $order[0],
                    'quantity' => $order[1],
                    'issue' => 'Cost must be greater than 0'
                ];
                continue;
            }

            $orders[] = [
                'ref' => $order[0],
                'quantity' => $order[1],
                'store_id' => $store_id
            ];
        }

        $failed_import_file_path = '';

        if (count($invalid_products) > 0) {

            $file_name = 'import-issue-' . Carbon::now()->format('Y-m-d-H-i-s') . '.xlsx';

            $new_csv = [];

            foreach ($invalid_products as $invalid_product) {
                $new_csv[] = [
                    $invalid_product['ref'],
                    $invalid_product['quantity'],
                    $invalid_product['issue']
                ];
            }

            $csv = (new Collection($new_csv))->storeExcel(
                $file_name,
                'public',
            );

            if ($csv) {

                if (FacadesFile::delete($file_path)) {
                    File::where('id', $file_id)->delete();
                }
                $failed_import_file_path = asset('storage/' . $file_name);
            }
        }

        $processed_orders = $this->processOrders($orders);

        foreach ($processed_orders as $processed_order) {
            $order = Order::create($processed_order);
            $order->products()->createMany($processed_order['products']);
        }

        $response = [
            'message' => 'Imported successfully',
            'failed_imports' => count($invalid_products),
            'failed_import_file' => $failed_import_file_path
        ];

        return response()->json($response);
    }

    //

    private function processOrders($orders)
    {

        $query_orders = Product::whereIn('ref', array_map(function ($a) {
            return trim(strtolower($a['ref']));
        }, $orders))->get();

        $map_orders_by_ref = [];

        foreach ($query_orders as $query_order) {

            // convert ref to lowercase
            $query_order['ref'] =  trim(strtolower($query_order['ref']));

            $map_orders_by_ref[$query_order['ref']] = $query_order;
        }
        foreach ($orders as $key => $order) {

            $order['ref'] =  trim(strtolower($order['ref']));

            $orders[$key]['product_id'] = $map_orders_by_ref[$order['ref']]['id'];
            $orders[$key]['product_cost'] = $map_orders_by_ref[$order['ref']]['cost'];
            $orders[$key]['is_paid'] = $map_orders_by_ref[$order['ref']]['is_paid'];
        }

        $processed_orders = [];

        // Create a unique order for each individual product

        foreach ($orders as $order) {

            $order_product_id = $order['product_id'];
            $order_product_quantity = $order['quantity'];
            $order_product_cost = $order['product_cost'];
            $order_product_is_paid = $order['is_paid'];
            $order_product_store_id = $order['store_id'];
            $order_product_total_amount = $order_product_is_paid ? 0 : $order['product_cost'] * $order['quantity'];

            $processed_orders[$order_product_id]['product_id'] = $order_product_id;
            $processed_orders[$order_product_id]['product_cost'] = $order_product_cost;
            $processed_orders[$order_product_id]['is_paid'] = $order_product_is_paid;

            $processed_orders[$order_product_id]['products'][] = [
                'user_id' => 1,
                'quantity' => $order_product_quantity,
                'store_id' => $order_product_store_id,
                'total_amount' => $order_product_total_amount
            ];
        }

        return $processed_orders;
    }
}
