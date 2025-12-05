<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\StampCorrectionRequest;
use Carbon\Carbon;

class StampCorrectionRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 対象の一般ユーザー（AdminUserSeederと一致）
        $targetEmails = [
            'reina.n@coachtech.com',
            'taro.y@coachtech.com',
            'issei.m@coachtech.com',
            'keikichi.y@coachtech.com',
            'tomomi.a@coachtech.com',
            'norio.n@coachtech.com',
        ];

        foreach ($targetEmails as $email) {
            $user = User::where('email', $email)->first();
            if (!$user) continue;

            // ---------------------------------------------------------
            // ケース1: 【承認待ち】の残業申請 (例: 12月5日)
            // ---------------------------------------------------------
            $pendingDate = '2025-12-05'; // 金曜日
            $attendancePending = Attendance::where('user_id', $user->id)
                                           ->where('date', $pendingDate)
                                           ->first();

            if ($attendancePending) {
                StampCorrectionRequest::create([
                    'user_id'        => $user->id,
                    'attendance_id'  => $attendancePending->id,
                    'reason'         => '業務多忙のため残業申請します。',
                    'new_start_time' => '08:00:00', // 開始時間はそのまま
                    'new_end_time'   => '20:00:00', // 18:00 -> 20:00 に変更依頼
                    'is_approved'    => false,      // 未承認
                    'status'         => '承認待ち',
                ]);
            }

            // ---------------------------------------------------------
            // ケース2: 【承認済み】の残業申請 (例: 12月8日)
            // ---------------------------------------------------------
            $approvedDate = '2025-12-08'; // 月曜日
            $attendanceApproved = Attendance::where('user_id', $user->id)
                                            ->where('date', $approvedDate)
                                            ->first();

            if ($attendanceApproved) {
                // 1. 勤怠データ自体を「承認された時間（残業後）」に更新しておく
                $attendanceApproved->update([
                    'end_time' => '21:00:00', // 実際のデータも更新
                ]);

                // 2. 承認済みの申請レコードを作成
                StampCorrectionRequest::create([
                    'user_id'        => $user->id,
                    'attendance_id'  => $attendanceApproved->id,
                    'reason'         => '緊急トラブル対応のため。',
                    'new_start_time' => '08:00:00',
                    'new_end_time'   => '21:00:00', // 18:00 -> 21:00 に変更
                    'is_approved'    => true,       // 承認済み
                    'status'         => '承認済み',
                ]);
            }
        }
    }
}
