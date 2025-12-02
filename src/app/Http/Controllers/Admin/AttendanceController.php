<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Http\Requests\AttendanceRequest;

class AttendanceController extends Controller
{
   public function list(AttendanceRequest $request)
    {
        // 1. 日付の取得（パラメータがない場合は今日）
        $date = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::today();
        
        // 日付フォーマット（表示用とリンク用）
        $currentDate = $date->format('Y-m-d');
        $displayDate = $date->format('Y/m/d');
        
        $previousDate = $date->copy()->subDay()->format('Y-m-d');
        $nextDate = $date->copy()->addDay()->format('Y-m-d');

        // 2. その日の勤怠データを取得（ページネーションなしで全員分表示が一般的ですが、人数が多い場合は考慮）
        $attendances = Attendance::with('user')
                        ->where('date', $currentDate)
                        ->get();

        return view('admin.attendance.list', compact('attendances', 'displayDate', 'previousDate', 'nextDate'));
    }

    // ... show, update メソッドはそのまま ...
    public function show($id)
    {
        $attendance = Attendance::with(['user', 'rests'])->findOrFail($id);
        return view('admin.attendance.show', compact('attendance'));
    }

      public function update(AttendanceRequest $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $attendance->update([
            'start_time' => $request->start_time,
            'end_time'   => $request->end_time,
            'status'     => $request->status,
            'reason'     => $request->reason, // ← ★これを追加！
        ]);

        return redirect()->route('admin.attendance.list', ['date' => $attendance->date])
                         ->with('message', '勤怠情報を修正しました。');
    }

  
  
}
