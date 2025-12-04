@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin_request_detail.css') }}">
@endsection

@section('content')
<div class="detail-wrapper">
    <h2 class="detail-title">勤怠詳細</h2>

    <div class="detail-container">
        <table class="detail-table">
            <!-- 名前 -->
            <tr>
                <th>名前</th>
                <td>{{ $correctionRequest->user->name }}</td>
            </tr>

            <!-- 日付 -->
            <tr>
                <th>日付</th>
                <td>
                    <div class="date-display">
                        <span>{{ \Carbon\Carbon::parse($attendance->date)->format('Y年') }}</span>
                        <span>{{ \Carbon\Carbon::parse($attendance->date)->format('n月j日') }}</span>
                    </div>
                </td>
            </tr>

            <!-- 出勤・退勤 -->
            <tr>
                <th>出勤・退勤</th>
                <td>
                    <div class="time-display">
                        {{-- 申請された新しい時間を表示 --}}
                        <span>{{ \Carbon\Carbon::parse($correctionRequest->new_start_time)->format('H:i') }}</span>
                        <span class="range-separator">～</span>
                        <span>{{ $correctionRequest->new_end_time ? \Carbon\Carbon::parse($correctionRequest->new_end_time)->format('H:i') : '' }}</span>
                    </div>
                </td>
            </tr>

            <!-- 休憩 -->
            @foreach($attendance->rests as $index => $rest)
            <tr>
                <th>休憩{{ $index > 0 ? $index + 1 : '' }}</th>
                <td>
                    <div class="time-display">
                        <span>{{ \Carbon\Carbon::parse($rest->start_time)->format('H:i') }}</span>
                        <span class="range-separator">～</span>
                        <span>{{ $rest->end_time ? \Carbon\Carbon::parse($rest->end_time)->format('H:i') : '' }}</span>
                    </div>
                </td>
            </tr>
            @endforeach

            <!-- 備考（修正理由） -->
            <tr>
                <th>備考</th>
                <td>
                    {{ $correctionRequest->reason }}
                </td>
            </tr>
        </table>
    </div>

    <div class="button-area">
        @if($correctionRequest->is_approved)
            {{-- 承認済みの場合 --}}
            <button class="btn-approved">承認済み</button>
        @else
            {{-- 未承認の場合 --}}
            <a href="{{ route('admin.stamp_correction_request.approve', $correctionRequest->id) }}" class="btn-approve" onclick="return confirm('承認しますか？')">承認</a>
        @endif
    </div>
</div>
@endsection