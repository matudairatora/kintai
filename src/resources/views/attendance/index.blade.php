@extends('layouts.app')

@section('css')
<style>
 
</style>
@endsection
    @section('content')
    <h1>勤怠管理システム</h1>
    <p>{{ Auth::user()->name }}さん、お疲れ様です！</p>
        @if(session('message'))
        <div style="color: green; font-weight: bold;">{{ session('message') }}</div>
        @endif
        @if(session('error'))
        <div style="color: red; font-weight: bold;">{{ session('error') }}</div>
        @endif

    <div style="display: flex; gap: 10px;">
        <form action="{{ route('attendance.store') }}" method="POST">
            @csrf
            <button type="submit" name="type" value="clock_in">出勤</button>
            <button type="submit" name="type" value="clock_out">退勤</button>
            <button type="submit" name="type" value="break_start">休憩開始</button>
            <button type="submit" name="type" value="break_end">休憩終了</button>
        </form>
    </div>
<p><a href="{{ route('attendance.list') }}">>> 過去の勤怠を見る</a></p>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">ログアウト</button>
    </form>
@endsection