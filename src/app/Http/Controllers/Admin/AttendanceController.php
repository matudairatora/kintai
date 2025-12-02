<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;

class AttendanceController extends Controller
{
   public function list()
    {
        // 全員の勤怠データを取得
        $attendances = Attendance::with('user')->orderBy('date', 'desc')->paginate(10);

        return view('admin.attendance.list', compact('attendances'));
    }
    public function show($id)
    {
        // 勤怠データと、それに紐づくユーザー・休憩情報を取得
        $attendance = Attendance::with(['user', 'rests'])->findOrFail($id);

        return view('admin.attendance.show', compact('attendance'));
    }

    // ▼ データを更新（修正）
    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        // 入力値を更新
        $attendance->update([
            'start_time' => $request->start_time,
            'end_time'   => $request->end_time,
            'status'     => $request->status,
        ]);

        return redirect()->route('admin.attendance.list')
                         ->with('message', '勤怠情報を修正しました。');
    }
}
