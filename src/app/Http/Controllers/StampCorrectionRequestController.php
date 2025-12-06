<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StampCorrectionRequest; 
use App\Models\Attendance;             
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AttendanceRequest;

class StampCorrectionRequestController extends Controller
{
    public function index()
    {
        // 承認待ち（is_approved が false）
        $pendingRequests = StampCorrectionRequest::where('user_id', Auth::id())
                                ->where('is_approved', false)
                                ->with('attendance') // 日付などを出すため
                                ->orderBy('created_at', 'desc')
                                ->get();

        // 承認済み（is_approved が true）
        $approvedRequests = StampCorrectionRequest::where('user_id', Auth::id())
                                ->where('is_approved', true)
                                ->with('attendance')
                                ->orderBy('created_at', 'desc')
                                ->get();

        return view('stamp_correction_request.index', compact('pendingRequests', 'approvedRequests'));
    }

    public function store(AttendanceRequest $request)
    {
        // 1. バリデーション
       // $request->validate([
         //   'attendance_id' => 'required|exists:attendances,id',
         //   'reason' => 'required|string|max:255',
         //   'start_time' => 'required',
         //   'end_time' => 'required|after:start_time', // 退勤は出勤より後であること
        //], [
          //  'reason.required' => '修正理由は必須です。',
          //  'start_time.required' => '出勤時間は必須です。',
          //  'end_time.required' => '退勤時間は必須です。',
          //  'end_time.after' => '退勤時間は出勤時間より後に設定してください。',
        //]);

        // 2. 権限チェック（自分の勤怠か）
        $attendance = Attendance::find($request->attendance_id);
        if ($attendance->user_id !== Auth::id()) {
            abort(403, '権限がありません。');
        }
        
        // 3. 申請データの保存
        StampCorrectionRequest::create([
            'user_id' => Auth::id(), // 必須
            'attendance_id' => $request->attendance_id,
            'reason' => $request->reason,
            'new_start_time' => $request->start_time, // 修正後の出勤時間
            'new_end_time' => $request->end_time,     // 修正後の退勤時間
            'status' => '承認待ち',
            'is_approved' => false,
        ]);

        // 4. 完了メッセージとともにリダイレクト
        return redirect()->route('attendance.show', $request->attendance_id)
                         ->with('message', '修正申請を送信しました。承認をお待ちください。');
    }
}