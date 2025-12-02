@extends('layouts.app')

@section('css')
<style>
    .date-nav {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 20px;
        margin: 20px 0;
    }
    .date-nav a {
        padding: 5px 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        background: #fff;
        text-decoration: none;
    }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
    th { background-color: #f2f2f2; }
</style>
@endsection

@section('content')
    <div style="text-align: center;">
        <h1>{{ $displayDate }}の勤怠</h1>

        <!-- 日付切り替えナビゲーション (FN035) -->
        <div class="date-nav">
            <a href="{{ route('admin.attendance.list', ['date' => $previousDate]) }}">&lt; 前日</a>
            <span style="font-size: 1.2em; font-weight: bold;">{{ $displayDate }}</span>
            <a href="{{ route('admin.attendance.list', ['date' => $nextDate]) }}">翌日 &gt;</a>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>名前</th>
                <th>勤務開始</th>
                <th>勤務終了</th>
                <th>休憩時間</th>
                <th>勤務時間</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendances as $attendance)
                <tr>
                    <td>{{ $attendance->user->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($attendance->start_time)->format('H:i') }}</td>
                    <td>{{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '' }}</td>
                    
                    <!-- モデルに追加したアクセサを利用 -->
                    <td>{{ $attendance->total_rest_time }}</td>
                    <td>{{ $attendance->total_work_time }}</td>
                    
                    <td>
                        <a href="{{ route('admin.attendance.show', $attendance->id) }}">詳細</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection