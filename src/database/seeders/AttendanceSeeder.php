<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $targetEmails = [
            'reina.n@coachtech.com',
            'taro.y@coachtech.com',
            'issei.m@coachtech.com',
            'keikichi.y@coachtech.com',
            'tomomi.a@coachtech.com',
            'norio.n@coachtech.com',
        ];

        // 対象期間：2025年12月1日 〜 2025年12月31日
        $startDate = Carbon::create(2025, 12, 1);
        $endDate   = Carbon::create(2025, 12, 31);

        foreach ($targetEmails as $email) {
            // ユーザーを取得
            $user = User::where('email', $email)->first();

            // ユーザーが存在しない場合はスキップ
            if (!$user) {
                continue;
            }
            if (is_null($user->email_verified_at)) {
                $user->email_verified_at = now();
                $user->save();
            }

            $currentDate = $startDate->copy();

            // 日付を1日ずつ進めながら処理
            while ($currentDate->lte($endDate)) {
                // ■「適度に休む」ロジック
                // ここではシンプルに「土日は休み」とします
                // (isWeekend() は土日なら true を返します)
                if (!$currentDate->isWeekend()) {
                    
                    // 1. 勤怠データの作成 (08:00 - 18:00)
                    $attendance = Attendance::create([
                        'user_id'    => $user->id,
                        'date'       => $currentDate->format('Y-m-d'),
                        'start_time' => '08:00:00',
                        'end_time'   => '18:00:00',
                        'status'     => '退勤済',
                    ]);

                    // 2. 休憩データの作成 (12:00 - 13:00 の1時間)
                    Rest::create([
                        'attendance_id' => $attendance->id,
                        'start_time'    => '12:00:00',
                        'end_time'      => '13:00:00',
                    ]);
                }

                // 次の日に進める
                $currentDate->addDay();
            }
        }
    }
}
