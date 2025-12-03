@extends('layouts.app')

@section('css')
<style>
    .page-title {
        font-size: 24px;
        font-weight: bold;
        text-align: center;
        margin-top: 30px;
    }
    .month-nav {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 20px;
        margin: 20px 0;
    }
    .month-nav a {
        text-decoration: none;
        color: #333;
        border: 1px solid #ccc;
        padding: 5px 10px;
        background: #fff;
    }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; background: #fff; }
    th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
    th { background-color: #f2f2f2; }
</style>
@endsection

@section('content')
    <div style="max-width: 900px; margin: 0 auto;">
        <h2 class="page-title">{{ $user->name }}さんの勤怠</h2>

        <!-- 月切り替え -->
        <div class="month-nav">
            <a href="{{ route('admin.attendance.staff_list', ['id' => $user->id, 'month' => $previousMonth]) }}">&lt; 前月</a>
            <span style="font-size: 1.2em; font-weight: bold;">{{ $currentMonthDisplay }}</span>
            <a href="{{ route('admin.attendance.staff_list', ['id' => $user->id, 'month' => $nextMonth]) }}">翌月 &gt;</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>日付</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendances as $attendance)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($attendance->date)->format('m/d') }}</td>
                    <td>{{ \Carbon\Carbon::parse($attendance->start_time)->format('H:i') }}</td>
                    <td>{{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '' }}</td>
                    <td>{{ $attendance->total_rest_time }}</td>
                    <td>{{ $attendance->total_work_time }}</td>
                    <td>
                        <a href="{{ route('admin.attendance.show', $attendance->id) }}">詳細</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- CSV出力ボタン（後で機能実装します） -->
        <div style="margin-top: 20px; text-align: right;">
            <button disabled style="background: #ccc; color: #fff; border: none; padding: 10px 20px;">CSV出力（未実装）</button>
        </div>
    </div>
@endsection