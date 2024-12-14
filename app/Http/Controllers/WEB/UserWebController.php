<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request; // This line was missing before

class UserWebController extends Controller
{
    public function index()
    {
        $users = User::paginate(10); // 10 là số lượng bản ghi mỗi trang

        return view('users.users', compact('users'));
    }
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->date_of_birth = $request->date_of_birth;
        $user->avatar_url = $request->avatar_url;
        $user->save();
        return redirect()->route('users.index');
    }
}
