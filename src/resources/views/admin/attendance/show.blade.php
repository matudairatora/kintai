@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin_attendance_detail.css') }}">
@endsection

@section('content')
<div class="detail-wrapper">
    <h2 class="detail-title">勤怠詳細</h2>

    <form action="{{ route('admin.attendance.update', $attendance->id) }}" method="POST" class="detail-form">
        @csrf
        @method('PATCH')

        {{-- ▼▼▼ エラーメッセージ表示を追加 ▼▼▼ --}}
        @if ($errors->any())
            <div style="background-color: #ffe6e6; color: red; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{-- ▲▲▲ 追加ここまで ▲▲▲ --}}

        <table class="detail-table">
            <!-- 名前 -->
            <tr>
                <th>名前</th>
                <td>
                    <span class="detail-text">{{ $attendance->user->name }}</span>
                </td>
            </tr>

            <!-- 日付 -->
            <tr>
                <th>日付</th>
                <td>
                    <span class="detail-text">
                        {{ \Carbon\Carbon::parse($attendance->date)->format('Y年n月j日') }}
                    </span>
                </td>
            </tr>

            <!-- 出勤・退勤 -->
            <tr>
                <th>出勤・退勤</th>
                <td>
                    {{-- old()を使って入力値を保持 --}}
                    <input type="time" name="start_time" class="detail-input" 
                           value="{{ old('start_time', \Carbon\Carbon::parse($attendance->start_time)->format('H:i')) }}">
                    <span class="range-separator">～</span>
                    <input type="time" name="end_time" class="detail-input" 
                           value="{{ old('end_time', $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '') }}">
                </td>
            </tr>

            <!-- 休憩（編集可能） -->
            @foreach($attendance->rests as $index => $rest)
            <tr>
                <th>休憩{{ $index + 1 }}</th>
                <td>
                    <input type="hidden" name="rests[{{ $rest->id }}][id]" value="{{ $rest->id }}">
                    
                    {{-- 配列形式のold値取得 --}}
                    <input type="time" name="rests[{{ $rest->id }}][start_time]" class="detail-input" 
                           value="{{ old('rests.'.$rest->id.'.start_time', \Carbon\Carbon::parse($rest->start_time)->format('H:i')) }}">
                    <span class="range-separator">～</span>
                    <input type="time" name="rests[{{ $rest->id }}][end_time]" class="detail-input" 
                           value="{{ old('rests.'.$rest->id.'.end_time', $rest->end_time ? \Carbon\Carbon::parse($rest->end_time)->format('H:i') : '') }}">
                </td>
            </tr>
            @endforeach

            <!-- 備考 -->
            <tr>
                <th>備考</th>
                <td>
                    <textarea name="reason" class="detail-textarea" placeholder="備考">{{ old('reason', $attendance->reason) }}</textarea>
                </td>
            </tr>
        </table>

        <div class="button-area">
            <button type="submit" class="btn-submit">修正</button>
        </div>

    </form>
</div>
@endsection