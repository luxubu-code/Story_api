<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
class ForgotPasswordController extends Controller
{
    public function sendNewPassWord(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
        $user = User::where('email', $request->email)->first();
        $new_password = Str::random(10);
        $user->password = Hash::make($new_password);
        $user->save();
        Mail::send('emails.new-password', ['password' => $new_password], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('Your New Password');
        });
        return response()->json([
            'response_code' => '200',
            'status'        => 'success',
            'message'       => 'success send new password',
            'new_password'  => $new_password,
        ]);
    }
}
