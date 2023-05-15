<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\helpers\ResponseHelper;

class AuthController extends Controller
{
    //
    public function register(Request $request){
            $request->validate(
                [
                    'name'=>'required|string|max:100',
                    'email'=>'required|email|unique:users',
                    'password'=>'required|min:8|max:20'
                ]
            );
            $user=new User();
            $user->name=$request->name;
            $user->email=$request->email;
            $user->password= \Hash::make($request->password);

            $user->save();

            $token = $user->createToken('Blog')->accessToken;
            return ResponseHelper::success(['access_token'=>$token]);
    }

    public function login(Request $request){
        $request->validate(
            [
                'email'=>'required|email',
                'password'=>'required'
            ]
            );
            $credentials = [
                'email' => $request->email,
                'password' => $request->password
            ];
            //return $credentials;
            if(Auth::attempt($credentials))
            {
                $user=auth()->user();
                $token = $user->createToken('Blog')->accessToken;
                return ResponseHelper::success(['access_token'=>$token]);
            }
            return "error";
 }
        public function logout(Request $request){
            $user=auth()->user();
            $user->token()->revoke();
            return ResponseHelper::success([],'Successfully logout');
            return $user;
        }
}
