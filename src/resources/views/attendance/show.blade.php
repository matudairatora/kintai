@extends('layouts.app')

@section('css')
<style>
 
</style>
@endsection
    @section('content')
    <h1>勤怠詳細</h1>

    <table>
        <tr>
            <th>日付</th>
            <td>{{ $attendance->date }}</td>
        </tr>
        <tr>
            <th>ステータス</th>
            <td>{{ $attendance->status }}</td>
        </tr>
        <tr>
            <th>出勤時間</th>
            <td>{{ $attendance->start_time }}</td>
        </tr>
        <tr>
            <th>退勤時間</th>
            <td>{{ $attendance->end_time ?? '--:--:--' }}</td>
        </tr>
    </table>
    <div style="margin-top: 30px; border-top: 1px solid #ccc; padding-top: 20px;">
        <h3>修正申請</h3>
        <p>打刻間違いなどがある場合は、理由を記入して申請してください。</p>

        @if(session('message'))
            <div style="color: green; font-weight: bold;">{{ session('message') }}</div>
        @endif

        <form action="{{ route('stamp_correction_request.store') }}" method="POST">
            @csrf
            <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">

            <div>
                <label>修正理由・内容：</label><br>
                <textarea name="reason" rows="3" cols="50" placeholder="例：退勤ボタンを押し忘れました。18:00退勤に修正お願いします。" required></textarea>
            </div>

            <button type="submit" style="margin-top: 10px;">申請する</button>
        </form>
    </div>

    <h3>休憩履歴</h3>
    @if($attendance->rests->isEmpty())
        <p>休憩記録はありません。</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>休憩開始</th>
                    <th>休憩終了</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendance->rests as $rest)
                <tr>
                    <td>{{ $rest->start_time }}</td>
                    <td>{{ $rest->end_time ?? '休憩中' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <a href="{{ route('attendance.list') }}">一覧に戻る</a>
@endsection