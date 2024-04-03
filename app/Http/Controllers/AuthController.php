<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Requests\Auth\UpdateInforUserRequest;
use App\Models\Category;
use App\Models\District;
use App\Models\User;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function actionLogin(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if ($token = JWTAuth::attempt($credentials)) {
            if(Auth::user()->is_block == 1){
                return response()->json(['error' => 'Your account has been locked'], 400);
            }
            return response()->json([
                'token' => $token,
                "id_role" => auth()->user()->id_role
            ], 200);
        }

        return response()->json(['error' => 'Email or password is not correct'], 400);
    }

    protected function createNewToken($token)
    {
        return [
            'token' => $token,
            'token_type' => 'bearer',
            'status' => true,
        ];
    }

    public function getDataUser()
    {
        return response()->json([
            'user' => auth()->user(),
        ],200);
    }

    public function actionLogout()
    {
        return response()->json([
            'message' => 'Logout successfully',
        ],200);
    }

    public function updateInfor(UpdateInforUserRequest $request)
    {
        $user = auth()->user();
        if($user){
            $user_dtb = User::find($user->id);
            $data = $request->all();
            $user_dtb->update($data);
            return response()->json(['message' => 'Update information successfully'], 200);
        }
        return response()->json(['error' => 'User is not correct'], 400);
    }
}
