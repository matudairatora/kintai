<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者ログイン | 勤怠管理システム</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin_login.css') }}">
</head>
<body>
    <header class="header">
        <div class="header__left">
            <img src="{{ asset('img/COACHTECHヘッダーロゴ.png') }}" alt="COACHTECH" class="header__logo">
        </div>
    </header>

    <main class="content">
        <div class="login-wrapper">
            <h1 class="login-title">管理者ログイン</h1>

            <form method="POST" action="{{ route('admin.login.submit') }}">
                @csrf
                
                <!-- メールアドレス -->
                <div class="form-group">
                    <label class="form-label">メールアドレス</label>
                    <input class="form-input"  name="email" value="{{ old('email') }}">
                </div>
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <!-- パスワード -->
                <div class="form-group">
                    <label class="form-label">パスワード</label>
                    <input class="form-input" type="password" name="password">
                </div>
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <button class="btn-login" type="submit">管理者ログインする</button>
            </form>
        </div>
    </main>
</body>
</html>