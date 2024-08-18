<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /** register new account */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);
        if ($validator->fails()) {

            return response()->json(
                [
                    'response_code' => '400',
                    'status'        => 'error',
                    'message'       => $validator->errors(),
                ]
            );
        }
        try {
            $dt        = Carbon::now();
            $join_date = $dt->toDayDateTimeString();

            $user = new User();
            $user->name         = $request->name;
            $user->email        = $request->email;
            $user->password     = Hash::make($request->password);
            $user->save();

            return response()->json([
                'response_code' => '200',
                'status'        => 'success',
                'message'       => 'success Register',
            ]);
        } catch (\Exception $e) {
            Log::info($e);


            return response()->json([
                'response_code' => '200',
                'status'        => 'error',
                'error'        => $e->getMessage()  ,
                'message'       => 'fail Register',
            ]);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            $email     = $request->email;
            $password  = $request->password;

            if (Auth::attempt(['email' => $email, 'password' => $password])) {
                /** @var \App\Models\User */
                $user = Auth::user();
                $accessToken = $user->createToken($user->email)->accessToken;
                $data = [
                    'response_code' => '200',
                    'status'        => 'success',
                    'message'       => 'Success Login',
                    'user_infor'    => $user,
                    'token'         => $accessToken
                ];
                return response()->json($data);
            } else {
                $data = [
                    'response_code' => '401',
                    'status'        => 'error',
                    'message'       => 'Unauthorised'
                ];
                return response()->json($data);
            }
        } catch (\Exception $e) {
            // Log chi tiết lỗi vào file log
            Log::error('Login Error: ' . $e->getMessage(), ['exception' => $e]);

            $data = [
                'response_code' => '500',  // Nên sử dụng mã 500 để chỉ lỗi server
                'status'        => 'error',
                'message'       => 'Fail Login',
                'error'         => $e->getMessage()
            ];
            return response()->json($data);
        }
    }


    /** user info */
    public function userInfo()
    {
        try {
            $userDataList = User::latest()->paginate(10);
            $data = [];
            $data['response_code']  = '200';
            $data['status']         = 'success';
            $data['message']        = 'success get user list';
            $data['data_user_list'] = $userDataList;
            return response()->json($data);
        } catch (\Exception $e) {
            Log::info($e);
            $data = [];
            $data['response_code']  = '400';
            $data['status']         = 'error';
            $data['message']        = 'fail get user list';
            return response()->json($data);
        }
    }
}
