<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Console\Input\Input;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users',
            'phone' => 'required',
            'password' => 'required|same:confirm_password|min:8',
            'confirm_password' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'حدث خطأ',
                'errors' => $validator->errors()
            ],422);
        }

        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'phone'=>$request->phone,
            'role'=>1,
            'password'=>Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'تم التسجيل بنجاح',
            'data' => $user
        ],200);
    }


    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'حدث خطأ',
                'errors' => $validator->errors()
            ],422);
        }

        $user = User::where('email',$request->email)->first();

        $userdata = array(
            'email' => $request->email,
            'password' => $request->password
          );

        if(Auth::attempt($userdata)){
            $token = $user->createToken('auth-token')->plainTextToken;
            return response()->json([
                'message' => 'تم الدخول بنجاح',
                'token'=>$token,
                'data'=>$user
            ],200);
        }else{
            return response()->json([
                'message' => 'بيانات الدخول غير صحيحة',
            ],400);
        }
    }
}
