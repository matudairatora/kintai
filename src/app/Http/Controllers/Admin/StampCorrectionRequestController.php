<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StampCorrectionRequest;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;

class StampCorrectionRequestController extends Controller
{
     public function index()
    {
        $requests = StampCorrectionRequest::with(['user', 'attendance'])
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('admin.stamp_correction_request.list', compact('requests'));
    }

    public function show($id)
    {
        // 休憩の修正申請情報もロード
        $correctionRequest = StampCorrectionRequest::with(['user', 'attendance.rests', 'stamp_correction_request_rests'])->findOrFail($id);
        
        $attendance = $correctionRequest->attendance;

        return view('admin.stamp_correction_request.show', compact('correctionRequest', 'attendance'));
    }

    // 承認アクション
    public function approve($id)
    {
        DB::transaction(function () use ($id) {
            // 休憩の申請データも含めて取得
            $stampRequest = StampCorrectionRequest::with('stamp_correction_request_rests')->findOrFail($id);
            $attendance = Attendance::find($stampRequest->attendance_id);

            if ($attendance) {
                // 1. 出退勤時間の更新
                $attendance->update([
                    'start_time' => $stampRequest->new_start_time,
                    'end_time'   => $stampRequest->new_end_time,
                    'reason'     => $stampRequest->reason, 
                ]);

                // 2. 休憩情報の更新（ここを追加！）
                $newRests = $stampRequest->stamp_correction_request_rests;
                
                // 修正申請に休憩データが含まれている場合のみ更新
                if ($newRests->isNotEmpty()) {
                    // 既存の休憩を一度削除して、新しいデータに入れ替える（洗い替え）
                    $attendance->rests()->delete();

                    foreach ($newRests as $newRest) {
                        $attendance->rests()->create([
                            'start_time' => $newRest->start_time,
                            'end_time'   => $newRest->end_time,
                        ]);
                    }
                }
            }

            // 3. ステータス更新
            $stampRequest->update([
                'is_approved' => true,
                'status'      => '承認済み', 
            ]);
        });

        return redirect()->back()->with('message', '申請を承認しました。');
    }
}
