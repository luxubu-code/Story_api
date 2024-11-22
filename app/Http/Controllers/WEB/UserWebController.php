<?php
namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserWebController extends Controller
{
    public function index()
    {
        // Lấy danh sách tất cả người dùng
        $users = User::all();

        // Trả danh sách người dùng vào view
        return view('users.users', compact('users'));
    }
}
