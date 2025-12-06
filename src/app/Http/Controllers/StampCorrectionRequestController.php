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
                                ->with('attendance') 
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
         //  権限チェック（自分の勤怠か）
        $attendance = Attendance::find($request->attendance_id);
        if ($attendance->user_id !== Auth::id()) {
            abort(403, '権限がありません。');
        }
        
        //  申請データの保存
        StampCorrectionRequest::create([
            'user_id' => Auth::id(), 
            'attendance_id' => $request->attendance_id,
            'reason' => $request->reason,
            'new_start_time' => $request->start_time, 
            'new_end_time' => $request->end_time,     
            'status' => '承認待ち',
            'is_approved' => false,
        ]);

    
        return redirect()->route('attendance.show', $request->attendance_id);
    }
}