@extends('layouts.app')

@section('css')
<style>
 
</style>
@endsection
    @section('content')
    <h1>【管理者】修正申請一覧</h1>

    <a href="{{ route('admin.attendance.list') }}">勤怠一覧に戻る</a>
    <br><br>

    @if(session('message'))
        <div style="color: blue;">{{ session('message') }}</div>
        <br>
    @endif

    <table>
        <thead>
            <tr>
                <th>申請者</th>
                <th>対象日</th>
                <th>理由</th>
                <th>状態</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($requests as $request)
                <tr>
                    <td>{{ $request->user->name }}</td>
                    <td>{{ $request->attendance->date }}</td>
                    <td>{{ $request->reason }}</td>
                    <td>
                        @if($request->is_approved)
                            <span class="approved">承認済</span>
                        @else
                            <span class="pending">未承認</span>
                        @endif
                    </td>
                    <td>
                        @if(!$request->is_approved)
                            <a href="{{ route('admin.stamp_correction_request.approve', $request->id) }}" onclick="return confirm('承認しますか？')">
                                [承認する]
                            </a>
                            <a href="{{ route('admin.attendance.show', $request->attendance->id) }}">
                                [勤怠編集へ]
                            </a>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection