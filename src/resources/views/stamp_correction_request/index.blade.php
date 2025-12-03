@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/request_list.css') }}">
@endsection

@section('content')
<div class="request-list-wrapper">
    {{-- タイトル --}}
    <h2 class="page-title">申請一覧</h2>

    <div class="tab-wrap">
        {{-- ラジオボタン --}}
        <input id="tab1" type="radio" name="tab-item" checked>
        <input id="tab2" type="radio" name="tab-item">

        {{-- タブラベル --}}
        <label class="tab-label" for="tab1">承認待ち</label>
        <label class="tab-label" for="tab2">承認済み</label>

        {{-- タブの下線 --}}
        <div class="tab-border"></div>

        {{-- ▼ 承認待ちコンテンツ --}}
        <div id="content1" class="tab-content">
            <table class="request-table">
                <thead>
                    <tr>
                        <th>状態</th>
                        <th>名前</th>
                        <th>対象日時</th>
                        <th>申請理由</th>
                        <th>申請日時</th>
                        <th>詳細</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingRequests as $request)
                    <tr>
                        <td>承認待ち</td>
                        <td>{{ $request->user->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($request->attendance->date)->format('Y/m/d') }}</td>
                        <td>{{ $request->reason }}</td>
                        <td>{{ $request->created_at->format('Y/m/d') }}</td>
                        <td>
                            <a href="{{ route('attendance.show', $request->attendance->id) }}" class="detail-link">詳細</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if($pendingRequests->isEmpty())
                <p style="text-align: center; margin-top: 20px; color: #999;">承認待ちの申請はありません。</p>
            @endif
        </div>

        {{-- ▼ 承認済みコンテンツ --}}
        <div id="content2" class="tab-content">
            <table class="request-table">
                <thead>
                    <tr>
                        <th>状態</th>
                        <th>名前</th>
                        <th>対象日時</th>
                        <th>申請理由</th>
                        <th>申請日時</th>
                        <th>詳細</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($approvedRequests as $request)
                    <tr>
                        <td>承認済み</td>
                        <td>{{ $request->user->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($request->attendance->date)->format('Y/m/d') }}</td>
                        <td>{{ $request->reason }}</td>
                        <td>{{ $request->created_at->format('Y/m/d') }}</td>
                        <td>
                            <a href="{{ route('attendance.show', $request->attendance->id) }}" class="detail-link">詳細</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if($approvedRequests->isEmpty())
                <p style="text-align: center; margin-top: 20px; color: #999;">承認済みの申請はありません。</p>
            @endif
        </div>
    </div>
</div>
@endsection