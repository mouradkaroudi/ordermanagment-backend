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
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
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

        $query = User::filter($request)->latest();

        if ($per_page == -1) {
            $query = $query->get();
        } else {
            $query = $query->paginate($per_page);
        }
        
        return (new UserCollection($query));
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

        // If the role is delegate,
        // than we need to check if abilities contains recieve:orders
        // if not, we added it

        $abilities = $fields['abilities'];

        if (!in_array('recieve:orders', $abilities)) {
            $abilities[] = 'recieve:orders';
        }

        if ($user) {

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
     * @param  \App\Models\UserRequest  $user
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
     * @param  \App\Models\UserRequest  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, User $user)
    {

        $name = $request->input('name');
        $username = $request->input('username');
        $email = $request->input('email');
        $role = $request->input('role');
        $password = $request->input('password');

        if($name) {
            $user->name = $name;
        }

        if($username && $username !== $user->username) {
            $user->username = $username;
        }

        if($email && $email !== $user->email) {
            $user->email = $email;
        }

        if($password) {
            $user->password = Hash::make($password);
        }

        if($role && $user->can('update', $user)) {
            $user->role = $role;
        }

        if($user->save()) {

            $abilities = $request->input('abilities');

            if($abilities && $user->can('update', $user)) {
                Usermeta::where(['user_id' => $user->id, 'key' => 'abilities'])->update([
                    'value' => serialize($abilities)
                ]);
            }

            return response('', 200);

        }

        return response()->json([
            'message' => 'Something went wrong.'
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserRequest  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if($user->delete()) {
            return response('', 200);
        }else{
            return response()->json([
                'message' => 'Something went wrong.'
            ], 400);    
        }
    }
}
