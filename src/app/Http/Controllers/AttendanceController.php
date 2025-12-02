<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Rest; 
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; 

class AttendanceController extends Controller
{
   public function index()
    {
        return view('attendance.index');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $timestamp = Carbon::now();
        $date = Carbon::today();

        // どのボタンが押されたかで処理を分岐
        // ビュー側で <button name="type" value="clock_in"> のように送ります
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
            return redirect()->back()->with('message', '出勤しました！');
        }

        // --- 退勤処理 ---
        if ($type === 'clock_out') {
            $attendance = Attendance::where('user_id', $user->id)->where('date', $date)->first();
            if (!$attendance) return redirect()->back()->with('error', '出勤していません。');
            
            $attendance->update([
                'end_time' => $timestamp,
                'status' => '退勤済',
            ]);
            return redirect()->back()->with('message', '退勤しました。');
        }

        // --- 休憩開始 ---
        if ($type === 'break_start') {
            $attendance = Attendance::where('user_id', $user->id)->where('date', $date)->first();
            if (!$attendance) return redirect()->back()->with('error', '出勤していません。');
            
            Rest::create(['attendance_id' => $attendance->id, 'start_time' => $timestamp]);
            $attendance->update(['status' => '休憩中']);
            
            return redirect()->back()->with('message', '休憩開始しました。');
        }

        // --- 休憩終了 ---
        if ($type === 'break_end') {
            $attendance = Attendance::where('user_id', $user->id)->where('date', $date)->first();
            $rest = Rest::where('attendance_id', $attendance->id)->whereNull('end_time')->first();
            
            if ($rest) {
                $rest->update(['end_time' => $timestamp]);
                $attendance->update(['status' => '出勤中']);
            }
            return redirect()->back()->with('message', '休憩終了しました。');
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

        return redirect()->back()->with('message', '休憩を開始しました。');
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

        // 「終わっていない休憩」を探して終了時間を入れる
        $rest = Rest::where('attendance_id', $attendance->id)
                    ->whereNull('end_time') // 終了時間がまだ空のものを探す
                    ->first();

        if ($rest) {
            $rest->update([
                'end_time' => Carbon::now(),
            ]);
        }

        // ステータスを出勤中に戻す
        $attendance->update(['status' => '出勤中']);

        return redirect()->back()->with('message', '休憩を終了しました。');
    }
    public function list()
    {
        $user = Auth::user();

        // 自分の勤怠を日付の新しい順に取得（1ページ5件）
        $attendances = Attendance::where('user_id', $user->id)
                                 ->orderBy('date', 'desc')
                                 ->paginate(5);

        return view('attendance.list', compact('attendances'));
    }

    public function show($id)
    {
        // データを取得（休憩データも一緒に持ってくる）
        $attendance = Attendance::with('rests')->find($id);

        // データがない、または自分のものでない場合はエラーにする
        if (!$attendance || $attendance->user_id !== Auth::id()) {
            abort(404); // または 403 Forbidden
        }

        return view('attendance.show', compact('attendance'));
    }
}
