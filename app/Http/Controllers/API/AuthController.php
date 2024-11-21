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
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Kreait\Firebase\Auth as FirebaseAuth;
use InvalidArgumentException;
use Google_Client;



class AuthController extends Controller
{
    /** register new account */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            // 'password' => 'required|string|min:8|confirmed',
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
            $user = new User();
            $user->email        = $request->email;
            $user->name         = 'user_' . Str::random(5);
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
                'error'        => $e->getMessage(),
                'message'       => 'fail Register',
            ]);
        }
    }

    public function googleAuth(Request $request)
    {
        // Lấy idToken từ request
        $idToken = $request->input('idToken');

        if (!$idToken) {
            return response()->json([
                'message' => 'ID token is required',
                'error' => 'The provided ID token is missing or invalid.'
            ], 400);
        }

        try {
            // Giải mã ID token mà bạn nhận được từ Google
            $decodedToken = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', $idToken)[1]))));

            if (isset($decodedToken->sub)) {
                $googleId = $decodedToken->sub; // Google ID
                $email = $decodedToken->email; // Email của người dùng
                $name = $decodedToken->name; // Tên của người dùng

                // Kiểm tra và cập nhật/tạo tài khoản người dùng
                $user = User::updateOrCreate(
                    ['email' => $email], // Kiểm tra dựa trên email
                    [
                        'google_id' => $googleId,
                        'name' => $name,
                        'password' => null
                    ]
                );

                // Đăng nhập người dùng
                Auth::login($user);

                // Tạo access token cho người dùng đã đăng nhập
                $accessToken = $user->createToken('authToken')->accessToken;

                // Trả về access token và thông tin người dùng
                return response()->json([
                    'token_type' => 'Bearer',
                    'user' => $user,
                    'access_token' => $accessToken,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Invalid ID token',
                    'error' => 'The provided ID token could not be verified. Please ensure the token is valid.'
                ], 401);
            }
        } catch (\Exception $e) {
            // Bắt tất cả các lỗi khác
            return response()->json([
                'message' => 'An error occurred during authentication',
                'error' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString() // Hiển thị chi tiết hơn lỗi stack trace (tuỳ chọn)
            ], 500);
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
                    'user'    => $user,
                    'access_token'         => $accessToken
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
    public function store(Request $request)
    {  // Ensure we get an instance of User
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user) {
            $user->fcm_token = $request->token;
            $user->save();
            return response()->json(['success' => true, 'message' => 'FCM Token saved successfully']);
        } else {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
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
