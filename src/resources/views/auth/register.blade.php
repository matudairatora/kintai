<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠管理システム</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">

</head>
<body>
    <header class="header">
        <div class="header__left">
            <img src="{{ asset('img/COACHTECHヘッダーロゴ.png') }}" alt="COACHTECH" class="header__logo">
            <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
        </div>

</header>

    <main class="content">


<div class="form-wrapper">
    <h1 class="form-title">会員登録</h1>

    @if ($errors->any())
        <div class="error-message">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf
        
        <!-- 名前 -->
        <div class="form-item">
            <label class="form-label">名前</label>
            <input class="form-input" type="text" name="name" value="{{ old('name') }}">
        </div>

        <!-- メールアドレス -->
        <div class="form-item">
            <label class="form-label">メールアドレス</label>
            <input class="form-input" type="email" name="email" value="{{ old('email') }}">
        </div>

        <!-- パスワード -->
        <div class="form-item">
            <label class="form-label">パスワード</label>
            <input class="form-input" type="password" name="password">
        </div>

        <!-- パスワード確認 -->
        <div class="form-item">
            <label class="form-label">パスワード確認</label>
            <input class="form-input" type="password" name="password_confirmation">
        </div>

        <button class="form-button" type="submit">登録する</button>
    </form>

    <div class="form-link">
        <a href="{{ route('login') }}">ログイン</a>
    </div>
</div>
 </main>
</body>
</html>