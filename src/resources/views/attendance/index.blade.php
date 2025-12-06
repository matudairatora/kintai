@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="attendance__content">
    
    <!-- メッセージ表示 -->
    @if(session('message'))
        <div class="attendance__alert attendance__alert--success">
            {{ session('message') }}
        </div>
    @endif
    @if(session('error'))
        <div class="attendance__alert attendance__alert--danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- ステータスと日時表示 -->
    <div class="attendance__panel">
        
        @if(isset($status))
            @if($status == 0)
                <div class="attendance__tag">勤務外</div>
            @elseif($status == 1)
                <div class="attendance__tag">出勤中</div>
            @elseif($status == 2)
                <div class="attendance__tag">休憩中</div>
            @elseif($status == 3)
                <div class="attendance__tag">退勤済</div>
            @endif
        @else
            <div class="attendance__tag">勤務外</div>
        @endif

        <!-- 日付 -->
        <div class="attendance__date">
            {{ \Carbon\Carbon::now()->format('Y年m月d日') }}({{ \Carbon\Carbon::now()->isoFormat('ddd') }})
        </div>

        <!-- 時間 -->
        <div class="attendance__time">
            {{ \Carbon\Carbon::now()->format('H:i') }}
        </div>
    </div>

    <!-- 打刻ボタンエリア -->
    <form action="{{ route('attendance.store') }}" method="POST">
        @csrf
        <div class="attendance__button-area">

            @if(!isset($status) || $status == 0)
                <button type="submit" name="type" value="clock_in" class="attendance__button attendance__button--black">出勤</button>
            
            @elseif($status == 1)
                <button type="submit" name="type" value="clock_out" class="attendance__button attendance__button--black">退勤</button>
                <button type="submit" name="type" value="break_start" class="attendance__button attendance__button--white">休憩入</button>
            
            @elseif($status == 2)
                <button type="submit" name="type" value="break_end" class="attendance__button attendance__button--white">休憩戻</button>
            
            @elseif($status == 3)
                <div class="attendance__message">お疲れ様でした。</div>
            
            @endif

        </div>
    </form>
    
</div>
@endsection