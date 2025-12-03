@extends('layouts.app')

@section('css')
<style>
    /* ページ全体のスタイル調整 */
    .staff-list-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 30px 0;
    }

    /* タイトルデザイン（左側の黒いバー） */
    .page-title {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 40px;
        padding-left: 15px;
        border-left: 5px solid #333; /* 黒い縦線 */
        text-align: left;
        color: #333;
    }

    /* テーブルデザイン */
    .staff-table {
        width: 100%;
        border-collapse: collapse; /* セルの隙間をなくす */
        background-color: #fff; /* 背景色を白に */
        box-shadow: 0 1px 3px rgba(0,0,0,0.1); /* 軽い影をつける */
        border-radius: 5px;
        overflow: hidden; /* 角丸を有効にするため */
    }

    .staff-table th, .staff-table td {
        padding: 15px 20px;
        text-align: center;
        font-size: 14px;
        color: #333;
    }

    /* ヘッダー行 */
    .staff-table th {
        font-weight: bold;
        border-bottom: 1px solid #ddd;
    }

    /* データ行 */
    .staff-table td {
        border-bottom: 1px solid #eee;
    }

    /* 詳細リンク */
    .detail-link {
        color: #333;
        text-decoration: none;
        font-weight: bold;
        cursor: pointer;
    }
    
    .detail-link:hover {
        text-decoration: underline;
    }
</style>
@endsection

@section('content')
    <div class="staff-list-container">
        <!-- タイトル -->
        <h2 class="page-title">スタッフ一覧</h2>

        <!-- 一覧テーブル -->
        <table class="staff-table">
            <thead>
                <tr>
                    <th>名前</th>
                    <th>メールアドレス</th>
                    <th>月次勤怠</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        {{-- スタッフ別勤怠一覧へのリンク --}}
                        {{-- ※ルートが未定義の場合は # にしています。実装後に書き換えてください --}}
                        {{-- <a href="{{ route('admin.attendance.staff', $user->id) }}" class="detail-link">詳細</a> --}}
                        <a href="{{ route('admin.attendance.staff_list', $user->id) }}" class="detail-link">詳細</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection