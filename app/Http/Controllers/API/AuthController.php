<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ErrorHelper;
use App\Http\Helpers\ResponseHelper;
use App\Http\Helpers\ValidationHelper;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Carbon\Carbon;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Jobs\UpdateUserProfileImage;
use Google\Rpc\Context\AttributeContext\Response;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        ValidationHelper::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        try {
            $user = new User();
            $user->email = $request->email;
            $user->name = 'user_' . Str::random(5);
            $user->password = Hash::make($request->password);
            $user->save();
            return ResponseHelper::success(new UserResource($user), 'Đăng ký thành công', 201);
        } catch (\Exception $e) {
            Log::error('Register Error: ' . $e->getMessage(), ['exception' => $e]);
            return ErrorHelper::serverError($e, 'Lỗi đăng ký');
        }
    }

    public function googleAuth(Request $request)
    {
        ValidationHelper::make($request->all(), [
            'idToken' => 'required|string'
        ]);

        try {
            $tokenParts = explode('.', $request->idToken);
            if (count($tokenParts) != 3) {
                return ErrorHelper::badRequest('Token không hợp lệ');
            }

            $payload = base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[1]));
            $decodedToken = json_decode($payload);

            if (!isset($decodedToken->sub) || !isset($decodedToken->email)) {
                return ErrorHelper::badRequest('Token thiếu thông tin cần thiết');
            }

            $user = User::where('email', $decodedToken->email)->first();

            if (!$user) {
                $user = User::create([
                    'google_id' => $decodedToken->sub,
                    'name' => $decodedToken->name ?? 'Google User',
                    'avatar_url' => $decodedToken->picture ?? null,
                    'email' => $decodedToken->email,
                    'email_verified_at' => now()
                ]);
            }

            $token = $user->createToken('googleAuth')->accessToken;

            return response()->json([
                'response_code' => '200',
                'status' => 'success',
                'message' => 'Đăng nhập Google thành công',
                'user' => new UserResource($user),
                'access_token' => $token
            ]);
        } catch (\Exception $e) {
            Log::error('Google Auth Error: ' . $e->getMessage(), [
                'exception' => $e,
                'token_data' => $request->except('idToken')
            ]);
            return ErrorHelper::serverError($e, 'Lỗi trong quá trình xác thực Google');
        }
    }

    public function login(Request $request)
    {
        ValidationHelper::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return ErrorHelper::unauthorized('Thông tin đăng nhập không chính xác');
            }

            $token = $user->createToken('authToken')->accessToken;

            return response()->json([
                'response_code' => '200',
                'status' => 'success',
                'message' => 'Đăng nhập thành công',
                'user' => new UserResource($user),
                'access_token' => $token
            ]);
        } catch (\Exception $e) {
            return ErrorHelper::serverError($e, 'Lỗi đăng nhập');
        }
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = auth('api')->user();
        if (!$user) {
            return ErrorHelper::notFound('Không tìm thấy người dùng');
        }

        $user->fcm_token = $request->token;
        $user->save();

        return response()->json([
            'response_code' => '200',
            'status' => 'success',
            'message' => 'Lưu FCM Token thành công'
        ]);
    }

    public function userInfo()
    {
        try {
            $user = auth('api')->user();
            if (!$user) {
                return ErrorHelper::unauthorized('Người dùng chưa đăng nhập');
            }

            return response()->json([
                'response_code' => '200',
                'status' => 'success',
                'message' => 'Lấy thông tin người dùng thành công',
                'data' => new UserResource($user),
            ]);
        } catch (\Exception $e) {
            return ErrorHelper::serverError($e, 'Đã xảy ra lỗi không mong muốn');
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = auth('api')->user();

            if (!$user) {
                return ErrorHelper::unauthorized('Người dùng chưa đăng nhập');
            }

            ValidationHelper::make($request->all(), [
                'name' => 'required|string|max:255|min:2',
                'date_of_birth' => 'required|date|before:today',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            ], [
                'name.required' => 'Tên không được để trống',
                'name.min' => 'Tên phải có ít nhất 2 ký tự',
                'date_of_birth.required' => 'Ngày sinh không được để trống',
                'date_of_birth.before' => 'Ngày sinh không hợp lệ',
                'image.image' => 'Tệp phải là hình ảnh',
                'image.mimes' => 'Chỉ chấp nhận các định dạng: jpeg, png, jpg, gif, svg',
                'image.max' => 'Kích thước ảnh tối đa 2MB'
            ]);

            $imageUrl = $user->avatar_url;
            $publicId = $user->public_id;

            if ($request->hasFile('image')) {
                try {
                    $imagePath = $request->file('image')->store('temp_images');
                    $uploadedFile = Cloudinary::upload(Storage::path($imagePath), [
                        'folder' => 'user_avatars',
                        'overwrite' => true,
                        'resource_type' => 'image',
                    ]);

                    if ($user->public_id) {
                        Cloudinary::destroy($user->public_id);
                    }

                    $imageUrl = $uploadedFile->getSecurePath();
                    $publicId = $uploadedFile->getPublicId();

                    if (Storage::exists($imagePath)) {
                        Storage::delete($imagePath);
                    }
                } catch (\Exception $imageException) {
                    Log::error('Lỗi xử lý ảnh: ' . $imageException->getMessage());
                    return ErrorHelper::serverError($imageException, 'Lỗi tải lên hình ảnh');
                }
            }
            /** @var \App\Models\User|null $user */
            $user->update([
                'name' => $request->input('name'),
                'date_of_birth' => $request->input('date_of_birth'),
                'avatar_url' => $imageUrl,
                'public_id' => $publicId,
            ]);

            DB::commit();

            return response()->json([
                'response_code' => '200',
                'status' => 'success',
                'message' => 'Cập nhật thông tin người dùng thành công',
                'data' => new UserResource($user),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi cập nhật tài khoản: ' . $e->getMessage());
            return ErrorHelper::serverError($e, 'Đã xảy ra lỗi không mong muốn');
        }
    }
}