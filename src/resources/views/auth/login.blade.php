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
        </div>

</header>

    <main class="content">
<div class="form-wrapper">
    <h1 class="form-title">ログイン</h1>

    <form method="POST" action="{{ route('login') }}">
        @csrf
        
        <!-- メールアドレス -->
        <div class="form-item">
            <label class="form-label">メールアドレス</label>
            <input class="form-input" name="email" value="{{ old('email') }}">
        </div>
        @error('email')
            <div class="error-message">{{ $message }}</div>
        @enderror

        <!-- パスワード -->
        <div class="form-item">
            <label class="form-label">パスワード</label>
            <input class="form-input" type="password" name="password">
        </div>
        @error('password')
            <div class="error-message">{{ $message }}</div>
        @enderror

        <button class="form-button" type="submit">ログインする</button>
    </form>

    <div class="form-link">
        <a href="{{ route('register') }}">会員登録はこちら</a>
    </div>
</div>
</main>
</body>
</html>