<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠管理システム</title>
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css') 
</head>
<body>
    <header class="header">
        <div class="header__left">
            <img src="{{ asset('img/COACHTECHヘッダーロゴ.png') }}" alt="COACHTECH" class="header__logo">
        </div>

       
        <nav class="header__right">
            <ul class="header-nav">
                @if(Auth::check())
                    {{-- ▼ 管理者(role=1)の場合 --}}
                    @if(Auth::user()->role == 1)
                        <li><a href="{{ route('admin.attendance.list') }}" class="header-nav__link">勤怠一覧</a></li>
                        <li><a href="{{ route('admin.staff.list') }}" class="header-nav__link">スタッフ一覧</a></li> 
                        <li><a href="{{ route('admin.stamp_correction_request.list') }}" class="header-nav__link">申請一覧</a></li>
                    
                    {{-- ▼ 一般ユーザー(role=0)の場合 --}}
                    @else
                        <li><a href="{{ route('attendance.index') }}" class="header-nav__link">勤怠</a></li>
                        <li><a href="{{ route('attendance.list') }}" class="header-nav__link">勤怠一覧</a></li>
                        <li><a href="{{ route('stamp_correction_request.index') }}" class="header-nav__link">申請</a></li>
                    @endif

                    {{-- ▼ 共通：ログアウトボタン --}}
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="header-nav__button">ログアウト</button>
                        </form>
                    </li>
                @endif
            </ul>
        </nav>
    </header>

    <main class="content">
        @yield('content')
    </main>
</body>
</html>
