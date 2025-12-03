@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_detail.css') }}">
@endsection

@section('content')
<div class="detail-wrapper">
    <h2 class="detail-title">勤怠詳細</h2>

    <form action="{{ route('stamp_correction_request.store') }}" method="POST" class="detail-form">
        @csrf
        <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">

        {{-- エラーメッセージ --}}
        @if ($errors->any())
            <div class="attendance__alert attendance__alert--danger" style="color: red; background-color: #ffe6e6; padding: 10px; margin-bottom: 20px; border-radius: 4px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        {{-- 完了メッセージ --}}
        @if (session('message'))
            <div class="attendance__alert" style="color: green; background-color: #e6fffa; padding: 10px; margin-bottom: 20px; border-radius: 4px;">
                {{ session('message') }}
            </div>
        @endif

        <table class="detail-table">
            <!-- 名前 -->
            <tr>
                <th>名前</th>
                <td>
                    <span class="user-name">{{ $attendance->user->name ?? Auth::user()->name }}</span>
                </td>
            </tr>

            <!-- 日付 -->
            <tr>
                <th>日付</th>
                <td>
                    <span class="detail-date-text">
                        {{ \Carbon\Carbon::parse($attendance->date)->format('Y年') }}
                        <span style="margin-left: 20px;">
                            {{ \Carbon\Carbon::parse($attendance->date)->format('n月j日') }}
                        </span>
                    </span>
                </td>
            </tr>

            <!-- 出勤・退勤 -->
            <tr>
                <th>出勤・退勤</th>
                <td>
                    {{-- 承認待ち または 承認済み の場合はテキスト表示 --}}
                    @if($is_pending || $is_approved)
                        <span class="detail-text">
                            {{ \Carbon\Carbon::parse($attendance->start_time)->format('H:i') }}
                        </span>
                        <span class="range-separator">～</span>
                        <span class="detail-text">
                            {{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '' }}
                        </span>
                    @else
                        {{-- 通常時：入力フォーム --}}
                        <input type="time" name="start_time" class="detail-input" 
                               value="{{ \Carbon\Carbon::parse($attendance->start_time)->format('H:i') }}">
                        <span class="range-separator">～</span>
                        <input type="time" name="end_time" class="detail-input" 
                               value="{{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '' }}">
                    @endif
                </td>
            </tr>

            <!-- 休憩 -->
            @foreach($attendance->rests as $index => $rest)
            <tr>
                <th>休憩{{ $index > 0 ? $index + 1 : '' }}</th>
                <td>
                    @if($is_pending || $is_approved)
                        <span class="detail-text">
                            {{ \Carbon\Carbon::parse($rest->start_time)->format('H:i') }}
                        </span>
                        <span class="range-separator">～</span>
                        <span class="detail-text">
                            {{ $rest->end_time ? \Carbon\Carbon::parse($rest->end_time)->format('H:i') : '' }}
                        </span>
                    @else
                        <input type="hidden" name="rest_ids[]" value="{{ $rest->id }}">
                        <input type="time" name="rest_starts[]" class="detail-input" 
                               value="{{ \Carbon\Carbon::parse($rest->start_time)->format('H:i') }}">
                        <span class="range-separator">～</span>
                        <input type="time" name="rest_ends[]" class="detail-input" 
                               value="{{ $rest->end_time ? \Carbon\Carbon::parse($rest->end_time)->format('H:i') : '' }}">
                    @endif
                </td>
            </tr>
            @endforeach

            <!-- 備考 -->
            <tr>
                <th>備考</th>
                <td>
                    @if($is_pending)
                        <div class="detail-textarea" style="background-color: #f9f9f9; border:none; color: #777;">
                           承認待ちのため修正できません。
                        </div>
                    @elseif($is_approved)
                        <div class="detail-textarea" style="background-color: #f9f9f9; border:none; color: #777;">
                           承認済みのため修正できません。
                        </div>
                    @else
                        <textarea name="reason" class="detail-textarea" rows="4" placeholder="修正理由を記述してください"></textarea>
                    @endif
                </td>
            </tr>
        </table>

        {{-- ボタン表示エリア --}}
        @if($is_pending)
            <div class="button-area" style="justify-content: flex-start;">
                <span class="pending-message">*承認待ちのため修正はできません。</span>
            </div>
        @elseif($is_approved)
            <div class="button-area">
                <button type="button" class="btn-approved">承認済み</button>
            </div>
        @else
            <div class="button-area">
                <button type="submit" class="btn-submit">修正</button>
            </div>
        @endif

    </form>
</div>
@endsection