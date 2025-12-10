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

        @if (session('message'))
            <div class="attendance__alert" >
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
                    @if($is_pending)
                        <span class="detail-text">
                            {{ \Carbon\Carbon::parse($latestRequest->new_start_time)->format('H:i') }}
                        </span>
                        <span class="range-separator">～</span>
                        <span class="detail-text">
                            {{ $latestRequest->new_end_time ? \Carbon\Carbon::parse($latestRequest->new_end_time)->format('H:i') : '' }}
                        </span>
                    @elseif($is_approved)
                        <span class="detail-text">
                            {{ \Carbon\Carbon::parse($attendance->start_time)->format('H:i') }}
                        </span>
                        <span class="range-separator">～</span>
                        <span class="detail-text">
                            {{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '' }}
                        </span>
                    @else
                        {{-- 通常時は入力欄を表示 --}}
                        <input type="time" name="start_time" class="detail-input" value="{{ old('start_time', \Carbon\Carbon::parse($attendance->start_time)->format('H:i')) }}">
                        <span class="range-separator">～</span>
                        <input type="time" name="end_time" class="detail-input" value="{{ old('end_time', $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '') }}">
                    @endif
                </td>
            </tr>
            @error('end_time')
            <tr><td colspan="2"><div class="error-message">{{ $message }}</div></td></tr>
            @enderror

            <!-- 休憩 -->
            @php
                if ($is_pending && isset($latestRequest)) {
                    $displayRests = $latestRequest->stamp_correction_request_rests;
                } else {
                    $displayRests = $attendance->rests;
                }
            @endphp

            @foreach($displayRests as $index => $rest)
            <tr>
                <th>休憩{{ $index + 1 }}</th>
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
                        <input type="hidden" name="rests[{{ $rest->id }}][id]" value="{{ $rest->id }}">
                        
                        <input type="time" name="rests[{{ $rest->id }}][start_time]" class="detail-input" 
                               value="{{ old('rests.'.$rest->id.'.start_time', \Carbon\Carbon::parse($rest->start_time)->format('H:i')) }}">
                        
                        <span class="range-separator">～</span>
                        
                        <input type="time" name="rests[{{ $rest->id }}][end_time]" class="detail-input" 
                               value="{{ old('rests.'.$rest->id.'.end_time', $rest->end_time ? \Carbon\Carbon::parse($rest->end_time)->format('H:i') : '') }}">
                    @endif
                </td>
            </tr>
            @endforeach

            @if(!($is_pending || $is_approved))
            <tr>
                <th>休憩{{ count($attendance->rests) + 1 }}</th>
                <td>
                    <input type="time" name="rests[new][start_time]" class="detail-input" value="{{ old('rests.new.start_time') }}">
                    <span class="range-separator">～</span>
                    <input type="time" name="rests[new][end_time]" class="detail-input" value="{{ old('rests.new.end_time') }}">
                </td>
            </tr>
            @endif
            
            @error('rests')
            <tr><td colspan="2"><div class="error-message">{{ $message }}</div></td></tr>
            @enderror

            <!-- 備考 -->
            <tr>
                <th>備考</th>
                <td>
                    @if($is_pending)
                        <div>{{ $latestRequest->reason }}</div>
                    @elseif($is_approved)
                        <div>{{ $attendance->reason }}</div>
                    @else
                        <textarea name="reason" class="detail-textarea" rows="4">{{ old('reason', $attendance->reason) }}</textarea>
                    @endif
                </td>
            </tr>
            @error('reason')
            <tr><td colspan="2"><div class="error-message">{{ $message }}</div></td></tr>
            @enderror
        </table>

        @if($is_pending)
            <div class="button-area">
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