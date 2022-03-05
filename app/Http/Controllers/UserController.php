<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Usermeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $request = request()->all();

        return (new UserCollection(User::filter($request)->latest()->paginate()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        

        $fields = $request->validated();


        $user = User::create([
            'name' => $fields['name'],
            'username' => $fields['username'],
            'email' => $fields['email'],
            'role' => $fields['role'],
            'password' => Hash::make($fields['password'])
        ]);

        if($user) {

            $abilities = serialize($fields['abilities']);

            Usermeta::create([
                'user_id' => $user->id,
                'key' => 'abilities',
                'value' => $abilities
            ]);
        }

        return response()->json($user);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $fields = $request->validated();

        $abilities = $fields['abilities'];

        $update = $user->update([
            'name' => $fields['name'],
            'username' => $fields['username'],
            'email' => $fields['email'],
            'role' => $fields['role'],
            'password' => Hash::make($fields['password'])
        ]);

        if($update) {
            // TODO: check if there is changes in abilities
            return response('', 200);
        }
        
        return response()->json([
            'message' => 'Something went wrong.'
        ], 400);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
