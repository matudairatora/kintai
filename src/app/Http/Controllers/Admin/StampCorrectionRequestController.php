<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StampCorrectionRequest;
use App\Models\Attendance;

class StampCorrectionRequestController extends Controller
{
    public function index()
    {
        // 申請データを取得
        $requests = StampCorrectionRequest::with(['user', 'attendance'])
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('admin.stamp_correction_request.list', compact('requests'));
    }

    public function show($id)
    {
        // 申請IDからデータを取得
        $correctionRequest = StampCorrectionRequest::with(['user', 'attendance.rests'])->findOrFail($id);
        
        // 関連する勤怠データ
        $attendance = $correctionRequest->attendance;

        return view('admin.stamp_correction_request.show', compact('correctionRequest', 'attendance'));
    }
    // 承認アクション
    public function approve($id)
    {
        // 対象の申請を探す
        $stampRequest = StampCorrectionRequest::findOrFail($id);

        // 1. 実際の勤怠テーブルの時間を修正する
        $attendance = Attendance::find($stampRequest->attendance_id);
        if ($attendance) {
            $attendance->update([
                'start_time' => $stampRequest->new_start_time,
                'end_time'   => $stampRequest->new_end_time,
            ]);
        }

        // 2. 申請ステータスを「承認済み」に変更する
        $stampRequest->update([
            'is_approved' => true,
            'status'      => '承認済み', 
        ]);

        

        return redirect()->back();
    }
}
