<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Rest;
use Carbon\Carbon;
use App\Http\Requests\AttendanceRequest;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceController extends Controller
{
   public function list(Request $request)
    {
        $date = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::today();
        $currentDate = $date->format('Y-m-d');
        $displayDate = $date->format('Y/m/d');
        $previousDate = $date->copy()->subDay()->format('Y-m-d');
        $nextDate = $date->copy()->addDay()->format('Y-m-d');

        $attendances = Attendance::with('user')
                        ->where('date', $currentDate)
                        ->get();

        return view('admin.attendance.list', compact('attendances', 'displayDate', 'previousDate', 'nextDate'));
    }

    public function show($id)
    {
        $attendance = Attendance::with(['user', 'rests'])->findOrFail($id);
        
        // 修正申請詳細画面へのリダイレクト判定などはここではなく、一覧のリンク先で制御済みと想定
        // ここは純粋な勤怠編集画面
        return view('admin.attendance.show', compact('attendance'));
    }

    public function update(AttendanceRequest $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        // 1. 勤怠本体の更新
        $attendance->update([
            'start_time' => $request->start_time,
            'end_time'   => $request->end_time,
            'reason'     => $request->reason,
        ]);

        // 2. 休憩時間の更新 (要件 FN038 対応)
        if ($request->has('rests')) {
            foreach ($request->rests as $restId => $restData) {
                $rest = Rest::find($restId);
                if ($rest && $rest->attendance_id == $attendance->id) {
                    $rest->update([
                        'start_time' => $restData['start_time'],
                        'end_time'   => $restData['end_time'],
                    ]);
                }
            }
        }

        return redirect()->route('admin.attendance.list', ['date' => $attendance->date])
                         ->with('message', '勤怠情報を修正しました。');
    }

    public function staffList(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $currentDate = $request->input('month') ? Carbon::parse($request->input('month')) : Carbon::now();
        $startOfMonth = $currentDate->copy()->startOfMonth();
        $endOfMonth = $currentDate->copy()->endOfMonth();

        $attendances = Attendance::where('user_id', $user->id)
                                 ->whereBetween('date', [$startOfMonth, $endOfMonth])
                                 ->get()
                                 ->keyBy(function($item) {
                                     return Carbon::parse($item->date)->format('Y-m-d');
                                 });

        $calendar = [];
        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $attendanceForDay = $attendances->get($dateStr);
            $calendar[] = [
                'date' => $dateStr,
                'date_display' => $date->format('m/d') . '(' . $date->isoFormat('ddd') . ')',
                'attendance' => $attendanceForDay,
            ];
        }

        $previousMonth = $currentDate->copy()->subMonth()->format('Y-m');
        $nextMonth = $currentDate->copy()->addMonth()->format('Y-m');
        $currentMonthDisplay = $currentDate->format('Y/m');

        // viewには month パラメータも渡してCSVリンクで使えるようにする
        return view('admin.attendance.staff_list', compact(
            'user', 'calendar', 'previousMonth', 'nextMonth', 'currentMonthDisplay', 'currentDate'
        ));
    }

    // ▼▼▼ 追加機能: CSV出力 (要件 FN045 対応) ▼▼▼
    public function exportCsv(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $currentDate = $request->input('month') ? Carbon::parse($request->input('month')) : Carbon::now();
        $startOfMonth = $currentDate->copy()->startOfMonth();
        $endOfMonth = $currentDate->copy()->endOfMonth();

        $attendances = Attendance::where('user_id', $user->id)
                                 ->whereBetween('date', [$startOfMonth, $endOfMonth])
                                 ->orderBy('date', 'asc')
                                 ->get();

        $csvHeader = ['日付', '出勤', '退勤', '休憩', '合計'];
        $csvData = [];

        foreach ($attendances as $attendance) {
            $csvData[] = [
                $attendance->date,
                Carbon::parse($attendance->start_time)->format('H:i'),
                $attendance->end_time ? Carbon::parse($attendance->end_time)->format('H:i') : '',
                $attendance->total_rest_time,
                $attendance->total_work_time,
            ];
        }

        $response = new StreamedResponse(function () use ($csvHeader, $csvData) {
            $handle = fopen('php://output', 'w');
            
            // 文字化け対策 (BOM付きUTF-8)
            fwrite($handle, "\xEF\xBB\xBF");
            
            fputcsv($handle, $csvHeader);

            foreach ($csvData as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $filename = 'attendance_' . $user->name . '_' . $currentDate->format('Ym') . '.csv';
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }
  
}
