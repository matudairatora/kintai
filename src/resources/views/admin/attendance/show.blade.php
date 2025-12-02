<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>【管理者】勤怠詳細・編集</title>
</head>
<body>
    <h1>勤怠詳細・編集</h1>
    <p>対象ユーザー：{{ $attendance->user->name }}</p>

    <form action="{{ route('admin.attendance.update', $attendance->id) }}" method="POST">
        @csrf
        @method('PATCH') <div>
            <label>日付：</label>
            <span>{{ $attendance->date }}</span>
        </div>
        <br>

        <div>
            <label>出勤時間：</label>
            <input type="time" name="start_time" value="{{ \Carbon\Carbon::parse($attendance->start_time)->format('H:i') }}">
        </div>
        <br>

        <div>
            <label>退勤時間：</label>
            <input type="time" name="end_time" value="{{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '' }}">
        </div>
        <br>

        <div>
            <label>ステータス：</label>
            <input type="text" name="status" value="{{ $attendance->status }}">
        </div>
        <br>

        <button type="submit">変更を保存する</button>
    </form>

    <hr>
    <h3>休憩ログ（参照のみ）</h3>
    <ul>
        @foreach($attendance->rests as $rest)
            <li>{{ $rest->start_time }} 〜 {{ $rest->end_time }}</li>
        @endforeach
    </ul>

    <a href="{{ route('admin.attendance.list') }}">一覧に戻る</a>
</body>
</html>