<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function handlle_login(LoginRequest $request)
    {
        $loginUsers = [
            'email' => $request->email,
            'password' => $request->password,
            'level' => "Khách hàng",
        ];
        $loginAdmin =
            [
                'email' => $request->email,
                'password' => $request->password,
                'level' => "Giám đốc",
            ];
        if (Auth::attempt($loginUsers)) {
            return redirect()->route('home');
        }
        if (Auth::attempt($loginAdmin)) {
            return redirect()->route('users.index');
        } else {
            Session::flash('error_email', 'email của bạn không tồn tại');
            Session::flash('error_password', 'mật khẩu của bạn không tồn tại');
            return redirect()->back();
        }
    }
    public function show_login()
    {
        return view('auth.login');
    }
    public function login(Request $request){
        $validator = Validator::make($request->all(),[
            'email'=> $request->email,
            'password'=>'required'
        ]);
        if($validator->fails()){
            return redirect()->back('')->withErrors($validator)->withInput();
        }
    }
    public function logout()
    {
         Auth::logout();
        return redirect()->route('auth.login');
    }
    public function register(){

        return view('auth.register');
    }
    public function handle_register(Request $request){
        $users = new User();
        $users->name = $request->name;
        $users->email = $request->email;
        $users->password = Hash::make($request->password);
        $users->confirm_password = Hash::make($request->confirm_password);
        $users->phone = $request->phone;
        $users->address = $request->address;
        $users->level = 'khách hàng';
        $users->save();
        return redirect()->route('auth.login')->with('success', 'Đăng ký thành công');
    }
}
