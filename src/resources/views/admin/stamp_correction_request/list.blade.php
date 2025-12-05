@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin_request_list.css') }}">
@endsection

@section('content')
<div class="request-list-wrapper">
    <h2 class="page-title">申請一覧</h2>

    {{-- コントローラーから渡された $requests をステータスで振り分け --}}
    @php
        $pendingRequests = $requests->where('is_approved', false);
        $approvedRequests = $requests->where('is_approved', true);
    @endphp

    <div class="tab-wrap">
        {{-- ラジオボタン（タブ切り替え用） --}}
        <input id="tab1" type="radio" name="tab-item" checked>
        <input id="tab2" type="radio" name="tab-item">

        {{-- タブラベル --}}
        <label class="tab-label" for="tab1">承認待ち</label>
        <label class="tab-label" for="tab2">承認済み</label>

        {{-- タブの下線 --}}
        <div class="tab-border"></div>

        {{-- ▼ コンテンツ1：承認待ち一覧 --}}
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
                            <a href="{{ route('admin.stamp_correction_request.show', $request->id) }}" class="detail-link">詳細</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if($pendingRequests->isEmpty())
                <p style="text-align: center; margin-top: 20px; color: #999;">承認待ちの申請はありません。</p>
            @endif
        </div>

        {{-- ▼ コンテンツ2：承認済み一覧 --}}
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
                            <a href="{{ route('admin.stamp_correction_request.show', $request->id) }}" class="detail-link">詳細</a>
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