<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerificationController extends Controller
{
    public function notice(Request $request)
    {
        // 既に認証済みならトップへリダイレクト
        return $request->user()->hasVerifiedEmail()
            ? redirect()->intended('/')
            : view('auth.verify-email');
    }

    // 2. メール内のリンクをクリックした時の処理
    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill(); // 認証完了としてマーク

        return redirect('/')->with('message', 'メール認証が完了しました！');
    }

    // 3. 認証メールの再送信
    public function send(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('message', '認証メールを再送しました。');
    }
}
