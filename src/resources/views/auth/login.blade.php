@extends('layouts.app')

@section('css')
<style>
  
</style>
@endsection
    @section('content')

    <h1>ログイン</h1>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        
        <div>
            <label>メールアドレス:</label>
            <input type="email" name="email" required>
        </div>

        <div>
            <label>パスワード:</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit">ログイン</button>
    </form>

    <p><a href="/register">会員登録はこちら</a></p>
@endsection