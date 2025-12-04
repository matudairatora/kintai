@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin_attendance_list.css') }}">
@endsection

@section('content')
    {{-- タイトル --}}
    <h2 class="page-title">
        {{ \Carbon\Carbon::parse($displayDate)->format('Y年n月j日') }}の勤怠
    </h2>

    {{-- 日付ナビゲーション --}}
    <div class="date-nav-wrapper">
        <a href="{{ route('admin.attendance.list', ['date' => $previousDate]) }}" class="date-nav__link">
            ← 前日
        </a>
        
        <div class="date-nav__current">
            <span>📅</span>
            {{ $displayDate }}
        </div>

        <a href="{{ route('admin.attendance.list', ['date' => $nextDate]) }}" class="date-nav__link">
            翌日 →
        </a>
    </div>

    {{-- 勤怠テーブル --}}
    <table class="admin-table">
        <thead>
            <tr>
                <th>名前</th>
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
                {{-- 名前 --}}
                <td style="text-align: left; padding-left: 20px;">{{ $attendance->user->name }}</td>

                {{-- 出勤 --}}
                <td>{{ \Carbon\Carbon::parse($attendance->start_time)->format('H:i') }}</td>

                {{-- 退勤 --}}
                <td>{{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '' }}</td>

                {{-- 休憩 (モデルのアクセサを利用) --}}
                <td>{{ $attendance->total_rest_time }}</td>

                {{-- 合計 (モデルのアクセサを利用) --}}
                <td>{{ $attendance->total_work_time }}</td>

                {{-- 詳細 --}}
                <td>
                    <a href="{{ route('admin.attendance.show', $attendance->id) }}" class="detail-link">詳細</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection