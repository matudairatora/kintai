@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_list.css') }}">
@endsection

@section('content')
    {{-- タイトルエリア --}}
    <h2 class="page-title">
        <span style="margin-right: 10px; font-weight:normal;">|</span>勤怠一覧
    </h2>

    {{-- 月ナビゲーション --}}
    <div class="month-nav">
        <a href="{{ route('attendance.list', ['month' => $previousMonth]) }}" class="month-nav__link">← 前月</a>
        
        <div class="month-nav__current">
            {{-- カレンダーアイコンがあればimgタグを入れる場所 --}}
            <span>📅</span>
            {{ $currentMonthDisplay }}
        </div>

        <a href="{{ route('attendance.list', ['month' => $nextMonth]) }}" class="month-nav__link">翌月 →</a>
    </div>

    {{-- 勤怠テーブル --}}
    <table class="attendance-table">
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
            @foreach ($calendar as $day)
            <tr>
                {{-- 日付 --}}
                <td>{{ $day['date_display'] }}</td>

                {{-- 勤怠データがある場合 --}}
                @if ($day['attendance'])
                    
                    {{-- 出勤時間 (H:i) --}}
                    <td>
                        {{ \Carbon\Carbon::parse($day['attendance']->start_time)->format('H:i') }}
                    </td>

                    {{-- 退勤時間 (H:i) --}}
                    <td>
                        @if ($day['attendance']->end_time)
                            {{ \Carbon\Carbon::parse($day['attendance']->end_time)->format('H:i') }}
                        @endif
                    </td>

                    {{-- 休憩時間合計 (H:i) --}}
                    <td>
                        @if ($day['attendance']->total_rest_time)
                            {{-- total_rest_timeが文字列(01:00:00)の場合でもCarbonでパースしてH:iにする --}}
                            {{ \Carbon\Carbon::parse($day['attendance']->total_rest_time)->format('H:i') }}
                        @endif
                    </td>

                    {{-- 勤務時間合計 (H:i) --}}
                    <td>
                        @if ($day['attendance']->total_work_time)
                            {{ \Carbon\Carbon::parse($day['attendance']->total_work_time)->format('H:i') }}
                        @endif
                    </td>

                    {{-- 詳細リンク --}}
                    <td>
                        <a href="{{ route('attendance.show', $day['attendance']->id) }}" class="detail-link">詳細</a>
                    </td>

                {{-- 勤怠データがない日 --}}
                @else
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection