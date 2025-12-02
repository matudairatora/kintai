<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>【管理者】勤怠一覧</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background-color: #e0e0e0; }
        .pagination { display: flex; list-style: none; gap: 10px; justify-content: center; }
    </style>
</head>
<body>
    <h1>【管理者】その日、全社員の勤怠一覧</h1>
    
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">ログアウト</button>
    </form>

    <br>

    <table>
        <thead>
            <tr>
                <th>名前</th>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>状態</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendances as $attendance)
                <tr>
                    <td>{{ $attendance->user->name }}</td> <td>{{ $attendance->date }}</td>
                    <td>{{ $attendance->start_time }}</td>
                    <td>{{ $attendance->end_time ?? '--:--:--' }}</td>
                    <td>{{ $attendance->status }}</td>
                    <td>
                        <a href="{{ route('admin.attendance.show', $attendance->id) }}">詳細・編集</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $attendances->links() }}
    </div>
</body>
</html>