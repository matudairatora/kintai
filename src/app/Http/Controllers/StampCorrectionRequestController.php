<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StampCorrectionRequest; 
use App\Models\Attendance;             
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AttendanceRequest;
use Illuminate\Support\Facades\DB;

class StampCorrectionRequestController extends Controller
{
    public function index()
    {
        // 承認待ち
        $pendingRequests = StampCorrectionRequest::where('user_id', Auth::id())
                                ->where('is_approved', false)
                                ->with(['attendance', 'stamp_correction_request_rests'])
                                ->orderBy('created_at', 'desc')
                                ->get();

        // 承認済み
        $approvedRequests = StampCorrectionRequest::where('user_id', Auth::id())
                                ->where('is_approved', true)
                                ->with(['attendance', 'stamp_correction_request_rests'])
                                ->orderBy('created_at', 'desc')
                                ->get();

        return view('stamp_correction_request.index', compact('pendingRequests', 'approvedRequests'));
    }

    public function store(AttendanceRequest $request)
    {
        // 権限チェック
        $attendance = Attendance::find($request->attendance_id);
        if ($attendance->user_id !== Auth::id()) {
            abort(403, '権限がありません。');
        }
        
        DB::transaction(function () use ($request) {
            $correctionRequest = StampCorrectionRequest::create([
                'user_id' => Auth::id(), 
                'attendance_id' => $request->attendance_id,
                'reason' => $request->reason,
                'new_start_time' => $request->start_time, 
                'new_end_time' => $request->end_time,     
                'status' => '承認待ち',
                'is_approved' => false,
            ]);
            
            if ($request->has('rests')) {
                foreach ($request->rests as $restData) {
                    // start_timeがあるデータのみ保存
                    if (!empty($restData['start_time'])) {
                        $correctionRequest->stamp_correction_request_rests()->create([
                            'start_time' => $restData['start_time'],
                            'end_time'   => $restData['end_time'] ?? null,
                        ]);
                    }
                }
            }
        });
    
        return redirect()->route('attendance.show', $request->attendance_id)
                         ->with('message', '承認申請を送信しました。');
    }
}