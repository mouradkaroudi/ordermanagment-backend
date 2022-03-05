<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Usermeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function index(Request $request)
    {

        if (!Auth::attempt($request->only('username', 'password'))) {
            return response()->json([
                'code' => 'invalid-credentials'
            ], 401);
        }

        $user = User::where('username', $request['username'])->firstOrFail();

        $user_abilities = [];

        // By default we grant user with owner role all permissions
        if($user->role == 'owner') {
            $user_abilities[] = '*';
        }else{
            
            $user_abilities = Usermeta::where(['user_id' => $user->id, 'key' => 'abilities'])->first('value');

            if(empty($user_abilities)) {
                return abort(403, 'There is not abilities set to this account. Please contact your manager');
            }

            $user_abilities = unserialize($user_abilities['value']);
    
        }

        
        $token = $user->createToken('auth_token', $user_abilities)->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                $user,
                'abilities' => $user_abilities
            ]
        ], 201);
    }
}
