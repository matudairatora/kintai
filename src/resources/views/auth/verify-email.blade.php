<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>メール認証 | 勤怠管理システム</title>
    
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/email_verify.css') }}">
</head>
<body>
    <header class="header">
        <div class="header__left">
            <img src="{{ asset('img/COACHTECHヘッダーロゴ.png') }}" alt="COACHTECH" class="header__logo">
        </div>
    </header>

    <main class="content">
        <div class="verify-wrapper">
            @if (session('message'))
                <div class="success-message">
                    {{ session('message') }}
                </div>
            @endif

            <p class="verify-text">
                登録していただいたメールアドレスに認証メールを送付しました。<br>
                メール認証を完了してください。
            </p>

            
            <a href="http://localhost:8025" target="_blank" class="btn-dummy-verify">認証はこちらから</a>

            <br>

            {{-- 「認証メールを再送する」リンク --}}
            <form method="POST" action="{{ route('verification.send') }}" class="resend-form">
                @csrf
                <button type="submit" class="btn-resend-link">認証メールを再送する</button>
            </form>
        </div>
    </main>
</body>
</html>