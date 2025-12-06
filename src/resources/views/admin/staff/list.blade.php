@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin_staff.css') }}">
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
                    <a href="{{ route('admin.attendance.staff_list', $user->id) }}" class="detail-link">詳細</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection