<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>会員登録</title>
</head>
<body>
    <h1>会員登録</h1>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf
        
        <div>
            <label>名前:</label>
            <input type="text" name="name" required>
        </div>

        <div>
            <label>メールアドレス:</label>
            <input type="email" name="email" required>
        </div>

        <div>
            <label>パスワード:</label>
            <input type="password" name="password" required>
        </div>

        <div>
            <label>パスワード（確認）:</label>
            <input type="password" name="password_confirmation" required>
        </div>

        <button type="submit">登録する</button>
    </form>
</body>
</html>