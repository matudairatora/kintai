<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>勤怠一覧</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        .pagination { display: flex; list-style: none; gap: 10px; justify-content: center; }
    </style>
</head>
<body>
    <h1>勤怠一覧</h1>
    
    <a href="{{ route('attendance.index') }}">← 打刻画面に戻る</a>

    <table>
        <thead>
            <tr>
                <th>日付</th>
                <th>勤務開始</th>
                <th>勤務終了</th>
                <th>状態</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendances as $attendance)
                <tr>
                    <td>{{ $attendance->date }}</td>
                    <td>{{ $attendance->start_time }}</td>
                    <td>{{ $attendance->end_time ?? '--:--:--' }}</td>
                    <td>{{ $attendance->status }}</td>
                    <td>
                        <a href="{{ route('attendance.show', $attendance->id) }}">詳細</a>
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