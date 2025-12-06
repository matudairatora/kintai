<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Rest; 
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; 
use App\Models\StampCorrectionRequest;


class AttendanceController extends Controller
{
    
  public function index()
    {
        $user = Auth::user();
        $date = Carbon::today();
        
        // 今日の勤怠レコードを取得
        $attendance = Attendance::where('user_id', $user->id)->where('date', $date)->first();

        // ステータス判定ロジック
        // 0=勤務外, 1=出勤中, 2=休憩中, 3=退勤済
        $status = 0; 

        if ($attendance) {
            if ($attendance->status == '退勤済' || $attendance->end_time !== null) {
                $status = 3;
            } elseif ($attendance->status == '休憩中') {
                $status = 2;
            } elseif ($attendance->status == '出勤中') {
                $status = 1;
            }
        }

        return view('attendance.index', compact('status'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $timestamp = Carbon::now();
        $date = Carbon::today();

        $type = $request->input('type');

        // --- 出勤処理 ---
        if ($type === 'clock_in') {
            // すでに出勤済みかチェック
            if (Attendance::where('user_id', $user->id)->where('date', $date)->exists()) {
                 return redirect()->back()->with('error', 'すでに出勤しています。');
            }
            Attendance::create([
                'user_id' => $user->id,
                'date' => $date,
                'start_time' => $timestamp,
                'status' => '出勤中',
            ]);
            return redirect()->back();
        }

        // --- 退勤処理 ---
        if ($type === 'clock_out') {
            $attendance = Attendance::where('user_id', $user->id)->where('date', $date)->first();
            if (!$attendance) return redirect()->back()->with('error', '出勤していません。');
            
            $attendance->update([
                'end_time' => $timestamp,
                'status' => '退勤済',
            ]);
            return redirect()->back();
        }

        // --- 休憩開始 ---
        if ($type === 'break_start') {
            $attendance = Attendance::where('user_id', $user->id)->where('date', $date)->first();
            if (!$attendance) return redirect()->back()->with('error', '出勤していません。');
            
            Rest::create(['attendance_id' => $attendance->id, 'start_time' => $timestamp]);
            $attendance->update(['status' => '休憩中']);
            
            return redirect()->back();
        }

        // --- 休憩終了 ---
        if ($type === 'break_end') {
            $attendance = Attendance::where('user_id', $user->id)->where('date', $date)->first();
            $rest = Rest::where('attendance_id', $attendance->id)->whereNull('end_time')->first();
            
            if ($rest) {
                $rest->update(['end_time' => $timestamp]);
                $attendance->update(['status' => '出勤中']);
            }
            return redirect()->back();
        }

        return redirect()->back();
    }
    
    
    public function breakStart()
    {
        $user = Auth::user();

        // 今日の勤怠を取得
        $attendance = Attendance::where('user_id', $user->id)
                            ->where('date', Carbon::today())
                            ->first();

        // エラーチェック
        if (!$attendance) {
            return redirect()->back()->with('error', '出勤していません。');
        }
        if ($attendance->status === '休憩中') {
            return redirect()->back()->with('error', 'すでに休憩中です。');
        }

        // 休憩テーブルに新しいレコードを作成
        Rest::create([
            'attendance_id' => $attendance->id,
            'start_time' => Carbon::now(),
        ]);

        // 勤怠自体のステータスも更新
        $attendance->update(['status' => '休憩中']);

        return redirect()->back();
    }

    // ▼ 休憩終了アクション
    public function breakEnd()
    {
        $user = Auth::user();

        $attendance = Attendance::where('user_id', $user->id)
                            ->where('date', Carbon::today())
                            ->first();

        if (!$attendance || $attendance->status !== '休憩中') {
            return redirect()->back()->with('error', '休憩中ではありません。');
        }

        $rest = Rest::where('attendance_id', $attendance->id)
                    ->whereNull('end_time') 
                    ->first();

        if ($rest) {
            $rest->update([
                'end_time' => Carbon::now(),
            ]);
        }

        $attendance->update(['status' => '出勤中']);

        return redirect()->back();
    }
    public function list(Request $request)
    {
        $user = Auth::user();

        // 1. カレンダー操作
        $currentDate = $request->input('month') 
            ? Carbon::parse($request->input('month')) 
            : Carbon::now();

        $startOfMonth = $currentDate->copy()->startOfMonth();
        $endOfMonth = $currentDate->copy()->endOfMonth();

        // 2. その月の勤怠データを取得し、日付(Y-m-d)をキーにしてコレクション化
        $attendances = Attendance::where('user_id', $user->id)
                                 ->whereBetween('date', [$startOfMonth, $endOfMonth])
                                 ->get()
                                 ->keyBy(function($item) {
                                     return Carbon::parse($item->date)->format('Y-m-d');
                                 });

        // 3. カレンダーデータの作成（勤怠がない日も含めるためのループ処理）
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

        // 4. 前月・翌月のリンク用データを作成
        $previousMonth = $currentDate->copy()->subMonth()->format('Y-m');
        $nextMonth = $currentDate->copy()->addMonth()->format('Y-m');
        $currentMonthDisplay = $currentDate->format('Y/m'); 

        return view('attendance.list', compact(
            'calendar', 
            'previousMonth', 
            'nextMonth', 
            'currentMonthDisplay'
        ));
    }

    public function show($id)
    {
        $attendance = Attendance::with('rests')->find($id);

        if (!$attendance || $attendance->user_id !== Auth::id()) {
            abort(404);
        }

        // 申請状況のチェック
        $is_pending = false;
        $is_approved = false;
        
        if (class_exists('App\Models\StampCorrectionRequest')) {
            // 最新の申請を取得してステータスを確認
            $latestRequest = StampCorrectionRequest::where('attendance_id', $attendance->id)
                                ->latest() 
                                ->first();
            
            if ($latestRequest) {
                if ($latestRequest->status == '承認待ち') {
                    $is_pending = true;
                } elseif ($latestRequest->status == '承認済み') {
                    $is_approved = true;
                }
            }
        }

        return view('attendance.show', compact('attendance', 'is_pending', 'is_approved'));
    }
}
