@extends('layouts.app')

@section('css')
<style>
 
</style>
@endsection
    @section('content')
    <h1>申請一覧</h1>

    <div class="tab-wrap">
        <input id="tab1" type="radio" name="tab-item" checked>
        <label class="tab-label" for="tab1">承認待ち</label>

        <input id="tab2" type="radio" name="tab-item">
        <label class="tab-label" for="tab2">承認済み</label>

        <div id="content1" class="tab-content">
            @if($pendingRequests->isEmpty())
                <p>承認待ちの申請はありません。</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>申請種別</th>
                            <th>申請日時</th>
                            <th>対象日</th>
                            <th>理由</th>
                            <th>詳細</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingRequests as $request)
                        <tr>
                            <td>修正申請</td>
                            <td>{{ $request->created_at->format('Y/m/d') }}</td>
                            <td>{{ $request->attendance->date }}</td>
                            <td>{{ $request->reason }}</td>
                            <td>
                                <a href="{{ route('attendance.show', $request->attendance->id) }}">詳細</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div id="content2" class="tab-content">
            @if($approvedRequests->isEmpty())
                <p>承認済みの申請はありません。</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>申請種別</th>
                            <th>申請日時</th>
                            <th>対象日</th>
                            <th>理由</th>
                            <th>詳細</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($approvedRequests as $request)
                        <tr>
                            <td>修正申請</td>
                            <td>{{ $request->created_at->format('Y/m/d') }}</td>
                            <td>{{ $request->attendance->date }}</td>
                            <td>{{ $request->reason }}</td>
                            <td>
                                <a href="{{ route('attendance.show', $request->attendance->id) }}">詳細</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection