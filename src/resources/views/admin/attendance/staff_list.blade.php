@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin_staff_list.css') }}">
@endsection

@section('content')
<div class="staff-list-wrapper">
    {{-- タイトル --}}
    <h2 class="page-title">{{ $user->name }}さんの勤怠</h2>

    {{-- 月ナビゲーション --}}
    <div class="month-nav">
        <a href="{{ route('admin.attendance.staff_list', ['id' => $user->id, 'month' => $previousMonth]) }}" class="month-nav__link">
            ← 前月
        </a>
        
        <div class="month-nav__current">
            <span>📅</span> {{ $currentMonthDisplay }}
        </div>

        <a href="{{ route('admin.attendance.staff_list', ['id' => $user->id, 'month' => $nextMonth]) }}" class="month-nav__link">
            翌月 →
        </a>
    </div>

    {{-- 勤怠テーブル --}}
    <table class="staff-table">
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
                <td>{{ $day['date_display'] }}</td>

                @if ($day['attendance'])
                    <td>{{ \Carbon\Carbon::parse($day['attendance']->start_time)->format('H:i') }}</td>
                    <td>{{ $day['attendance']->end_time ? \Carbon\Carbon::parse($day['attendance']->end_time)->format('H:i') : '' }}</td>
                    <td>{{ $day['attendance']->total_rest_time }}</td>
                    <td>{{ $day['attendance']->total_work_time }}</td>
                    <td>
                        <a href="{{ route('admin.attendance.show', $day['attendance']->id) }}" class="detail-link">詳細</a>
                    </td>
                @else
                    <td></td><td></td><td></td><td></td><td></td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
    
    {{-- CSV出力ボタン --}}
    <div class="button-area">
        <form action="{{ route('admin.attendance.csv_export', $user->id) }}" method="GET">
            {{-- 現在表示している月を送信 --}}
            <input type="hidden" name="month" value="{{ $currentDate->format('Y-m') }}">
            <button type="submit" class="btn-csv">CSV出力</button>
        </form>
    </div>
</div>
@endsection