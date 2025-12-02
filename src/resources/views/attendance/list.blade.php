@extends('layouts.app')

@section('css')
<style>
 
</style>
@endsection
    @section('content')
    <div style="text-align: center; margin-top: 30px;">
        <h1>勤怠一覧</h1>
        
        <div class="month-nav">
            <a href="{{ route('attendance.list', ['month' => $previousMonth]) }}">&lt; 前月</a>
            <span style="font-size: 1.2em; font-weight: bold;">{{ $currentMonthDisplay }}</span>
            <a href="{{ route('attendance.list', ['month' => $nextMonth]) }}">翌月 &gt;</a>
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
                    <td>{{ \Carbon\Carbon::parse($attendance->date)->format('m/d') }} ({{ \Carbon\Carbon::parse($attendance->date)->isoFormat('ddd') }})</td>
                    <td>{{ \Carbon\Carbon::parse($attendance->start_time)->format('H:i') }}</td>
                    <td>{{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '' }}</td>
                    
                    <td>{{ $attendance->total_rest_time }}</td>
                    <td>{{ $attendance->total_work_time }}</td>
                    
                    <td>
                        <a href="{{ route('attendance.show', $attendance->id) }}">詳細</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection