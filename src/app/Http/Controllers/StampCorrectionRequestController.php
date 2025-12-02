<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StampCorrectionRequest; 
use App\Models\Attendance;             
use Illuminate\Support\Facades\Auth;

class StampCorrectionRequestController extends Controller
{
    // 修正申請を保存するアクション
    public function store(Request $request)
    {
        // 入力チェック（理由は必須）
        $request->validate([
            'attendance_id' => 'required|exists:attendances,id',
            'reason' => 'required|string|max:255',
        ]);

        // 自分の勤怠データか確認（不正防止）
        $attendance = Attendance::find($request->attendance_id);
        if ($attendance->user_id !== Auth::id()) {
            abort(403, '権限がありません。');
        }

        // 申請を作成
        StampCorrectionRequest::create([
            'user_id' => Auth::id(),
            'attendance_id' => $request->attendance_id,
            'reason' => $request->reason,
            'is_approved' => false, // 最初は「未承認」
        ]);

        return redirect()->back()->with('message', '修正申請を送信しました。承認をお待ちください。');
    }
}