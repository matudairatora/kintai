<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\StampCorrectionRequest;
use Carbon\Carbon;

class AttendanceRequestTest extends TestCase
{
    use RefreshDatabase;

        /**
     * ID: 9 勤怠一覧情報取得機能（一般ユーザー）
     */
    public function test_user_can_view_attendance_list()
    {
        $user = User::factory()->create();
        // 現在の日付で勤怠を作成
        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        $response = $this->actingAs($user)->get(route('attendance.list'));
        
        $response->assertStatus(200);
        $response->assertSee(Carbon::now()->format('Y/m')); 
        $response->assertSee('詳細'); 

        // 月移動ボタンの確認
        $response->assertSee('前月');
        $response->assertSee('翌月');
    }

    /**
     * ID: 10 勤怠詳細情報取得機能
     */
    public function test_user_can_view_attendance_detail()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-12-06',
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        $response = $this->actingAs($user)->get(route('attendance.show', $attendance->id));
        
        $response->assertStatus(200);
        $response->assertSee($user->name); 
        
        // HTML内で改行されている、年と月日を分けて確認
        $response->assertSee('2025年');
        $response->assertSee('12月6日');
        
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    /**
     * ID: 11 勤怠詳細情報修正機能（基本バリデーション）
     */
    public function test_correction_request_validation()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        // 不正な時間（出勤 > 退勤）
        $response = $this->actingAs($user)->post(route('stamp_correction_request.store'), [
            'attendance_id' => $attendance->id,
            'start_time' => '19:00', 
            'end_time' => '18:00',
            'reason' => '修正理由',
        ]);
        
        // start_time にエラーが出る実装になっているため確認
        $response->assertSessionHasErrors(['start_time']);

        // 備考未入力
        $response = $this->actingAs($user)->post(route('stamp_correction_request.store'), [
            'attendance_id' => $attendance->id,
            'start_time' => '09:00',
            'end_time' => '18:00',
            'reason' => '',
        ]);
        $response->assertSessionHasErrors('reason');
    }

    /**
     * ID: 11 勤怠詳細情報修正機能（休憩時間のバリデーション）
     */
    public function test_correction_request_break_time_validation()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        // 1. 休憩開始時間が勤務時間外（退勤より後）
        $response = $this->actingAs($user)->post(route('stamp_correction_request.store'), [
            'attendance_id' => $attendance->id,
            'start_time' => '09:00',
            'end_time' => '18:00',
            // rests配列としてデータを送信
            'rests' => [
                [
                    'start_time' => '19:00', // 退勤(18:00)より後
                    'end_time' => '19:30',
                ]
            ],
            'reason' => '修正理由',
        ]);
        $response->assertSessionHasErrors('rests'); 

        // 2. 休憩終了時間が勤務時間外（退勤より後）
        $response = $this->actingAs($user)->post(route('stamp_correction_request.store'), [
            'attendance_id' => $attendance->id,
            'start_time' => '09:00',
            'end_time' => '18:00',
            'rests' => [
                [
                    'start_time' => '12:00',
                    'end_time' => '19:00', // 退勤(18:00)より後
                ]
            ],
            'reason' => '修正理由',
        ]);
        $response->assertSessionHasErrors('rests');

        // 3. 休憩開始 > 休憩終了 (矛盾)
        $response = $this->actingAs($user)->post(route('stamp_correction_request.store'), [
            'attendance_id' => $attendance->id,
            'start_time' => '09:00',
            'end_time' => '18:00',
            'rests' => [
                [
                    'start_time' => '13:00', 
                    'end_time' => '12:00', // 開始より前
                ]
            ],
            'reason' => '修正理由',
        ]);
        $response->assertSessionHasErrors('rests');
    }

    /**
     * ID: 11 勤怠詳細情報修正機能（申請実行とステータス表示）
     */
    public function test_user_can_submit_correction_request()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('stamp_correction_request.store'), [
            'attendance_id' => $attendance->id,
            'start_time' => '10:00',
            'end_time' => '19:00',
            'reason' => '打刻忘れのため',
        ]);

        $response->assertRedirect(route('attendance.show', $attendance->id));

        $this->assertDatabaseHas('stamp_correction_requests', [
            'attendance_id' => $attendance->id,
            'new_start_time' => '10:00:00',
            'new_end_time' => '19:00:00',
            'reason' => '打刻忘れのため',
            'is_approved' => 0,
        ]);

        $response = $this->actingAs($user)->get(route('stamp_correction_request.index'));
        $response->assertSee('承認待ち');
        $response->assertSee('詳細');
    }
}
