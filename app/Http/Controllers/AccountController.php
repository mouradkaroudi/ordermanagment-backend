<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = request()->user();
        $user['abilities'] = $user->currentAccessToken()->abilities;
        return $user;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        
        $user = Auth::user();

        $request->validate([
            'name' => ['sometimes'],
            'username' => ['sometimes', Rule::unique('users')->ignore($user->id)],
            'email' => ['sometimes', Rule::unique('users')->ignore($user->id)],
            'currentPwd' => ['sometimes', function($input) {
                return Hash::check($input, Auth::user()->password);
            }],
            'newPwd' => ['sometimes', 'min:6'],
            'confirmNewPwd' => ['required_with:newPwd', 'same:newPwd'],
        ]);

        $name = $request->input('name');
        $username = $request->input('username');
        $email = $request->input('email');
        $currentPwd = $request->input('currentPwd');
        $newPwd = $request->input('newPwd');    
        $confirmNewPwd = $request->input('confirmNewPwd');

        $updateArray = [];

        if($name) {
            $updateArray['name'] = $name;
        }

        if($username && $username !== $user->username) {
            $updateArray['username'] = $username;
        }

        if($email && $email !== $user->email) {
            $updateArray['email'] = $email;
        }

        if($currentPwd && $newPwd && $confirmNewPwd) {
            $updateArray['password']= Hash::make($newPwd);
        }

        $update = User::where('id', $user->id)->update($updateArray);

        if($update) {
            return response('', 200);
        }
        
        return response()->json([
            'message' => 'Something went wrong.'
        ], 400);

    }

    public function updateFcmToken(Request $request) {
        
        $user = Auth::user();

        $token = $request->input('token');

        $update = User::where('id', $user->id)->update([
            'fcm_token' => $token
        ]);

        if($update) {
            return response('', 200);
        }
        
        return response()->json([
            'message' => 'Something went wrong.'
        ], 400);

    }

}
