<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // ログインしていて、かつ role が 1 (管理者) なら通す
        if (Auth::check() && Auth::user()->role == 1) {
            return $next($request);
        }

        // ダメならログイン画面へ追い返す（または403エラー）
        return redirect('/login')->with('error', '管理者権限がありません。');
    }
}
