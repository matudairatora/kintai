<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StampCorrectionRequest;

class StampCorrectionRequestController extends Controller
{
    public function index()
    {
        // 申請データを取得（ユーザー情報と、対象の勤怠情報も一緒に）
        $requests = StampCorrectionRequest::with(['user', 'attendance'])
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('admin.stamp_correction_request.list', compact('requests'));
    }

    // 承認アクション
    public function approve($id)
    {
        // 対象の申請を探す
        $request = StampCorrectionRequest::findOrFail($id);

        // 承認済みにする
        $request->update([
            'is_approved' => true,
        ]);

        return redirect()->back()->with('message', '申請を承認しました。');
    }
}
