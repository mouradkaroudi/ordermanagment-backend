<?php

namespace App\Http\Controllers;

use App\Http\Resources\DeliveryMethodCollection;
use App\Http\Resources\DeliveryMethodResource;
use App\Models\DeliveryMethod;
use Illuminate\Http\Request;

class DeliveryMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $request = request()->all();
        $per_page = $request['per_page'] ?? 50;

        $query = DeliveryMethod::latest();

        if ($per_page == -1) {
            $query = $query->get();
        } else {
            $query = $query->paginate($per_page);
        }

        return (new DeliveryMethodCollection($query));
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
            'name' => ['required'],
            'commission' => ['required', 'numeric'],
            'min' => ['required', 'numeric'],
            'max' => ['required', 'numeric'],
        ]);

        $deliveryMethod = DeliveryMethod::create([
            'name' => $request->input('name'),
            'commission' => $request->input('commission'),
            'min' => $request->input('min'),
            'max' => $request->input('max')
        ]);

        return response()->json($deliveryMethod);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DeliveryMethod  $deliveryMethod
     * @return \Illuminate\Http\Response
     */
    public function show(DeliveryMethod $deliveryMethod)
    {
        return new DeliveryMethodResource($deliveryMethod);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DeliveryMethod  $deliveryMethod
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DeliveryMethod $deliveryMethod)
    {
        $request->validate([
            'name' => ['required'],
            'commission' => ['required', 'numeric'],
            'min' => ['required', 'numeric'],
            'max' => ['required', 'numeric'],
        ]);

        $fields = [
            'name' => $request->input('name'),
            'commission' => $request->input('commission'),
            'min' => $request->input('min'),
            'max' => $request->input('max')
        ];

        if ($deliveryMethod->update($fields)) {
            return response('', 200);
        }

        return response()->json([
            'message' => 'Something went wrong.'
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DeliveryMethod  $deliveryMethod
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeliveryMethod $deliveryMethod)
    {
        if ($deliveryMethod->delete()) {
            return response('', 200);
        } else {
            return response()->json([
                'message' => 'Something went wrong.'
            ], 400);
        }
    }
}
