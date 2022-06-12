<?php

namespace App\Http\Controllers;

use App\Http\Resources\SupplierCollection;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $request = request()->all();

        $query = Supplier::filter($request)->latest();

        if(isset($request['per_page']) && $request['per_page'] == -1) {
            $query = $query->get();
        }else{
            $query = $query->paginate();
        }

        return (new SupplierCollection($query));
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
            'phone' => ['required'],
            'location_id' => ['required', 'exists:App\Models\Location,id']
        ]);

        $supplier = Supplier::create([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'location_id' => $request->input('location_id'),
        ]);

        return response()->json($supplier);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Supplier $supplier)
    {
        return new SupplierResource($supplier);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => ['required'],
            'phone' => ['required'],
            'location_id' => ['required', 'exists:App\Models\Location,id']
        ]);

        $fields = [
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'location_id' => $request->input('location_id'),
        ];

        if($supplier->update($fields)) {
            return response('', 200);
        }

        return response()->json([
            'message' => 'Something went wrong.'
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Supplier $supplier)
    {
        if($supplier->delete()) {
            return response('', 200);
        }else{
            return response()->json([
                'message' => 'Something went wrong.'
            ], 400);    
        }
    }
}
