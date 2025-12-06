<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest; 

class AuthController extends Controller
{
    // ログイン画面表示
    public function showLoginForm()
    {
        return view('admin.login');
    }

    // ログイン処理
    public function login(LoginRequest $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // roleが1（管理者）かチェック
            if (Auth::user()->role == 1) {
                return redirect()->route('admin.attendance.list'); 
            }

            // 管理者でない場合はログアウトさせて戻す
            Auth::logout();
            return back()->withErrors([
                'email' => '管理者権限がありません。',
            ]);
        }

        return back()->withErrors([
            'email' => 'ログイン情報が登録されていません。',
        ]);
    }
}