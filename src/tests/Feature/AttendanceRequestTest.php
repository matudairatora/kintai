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
        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::today()->format('Y-m-d'), 
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        $response = $this->actingAs($user)->get(route('attendance.list'));
        
        $response->assertStatus(200);
        $response->assertSee(Carbon::now()->format('Y/m')); 
        $response->assertSee('詳細'); 

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

        $response = $this->actingAs($user)->post(route('stamp_correction_request.store'), [
            'attendance_id' => $attendance->id,
            'start_time' => '19:00', 
            'end_time' => '18:00',
            'reason' => '修正理由',
        ]);
        
        $response->assertSessionHasErrors(['start_time']);

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

        $response = $this->actingAs($user)->post(route('stamp_correction_request.store'), [
            'attendance_id' => $attendance->id,
            'start_time' => '09:00',
            'end_time' => '18:00',
            'rests' => [
                [
                    'start_time' => '19:00', 
                    'end_time' => '19:30',
                ]
            ],
            'reason' => '修正理由',
        ]);
        $response->assertSessionHasErrors('rests'); 

        $response = $this->actingAs($user)->post(route('stamp_correction_request.store'), [
            'attendance_id' => $attendance->id,
            'start_time' => '09:00',
            'end_time' => '18:00',
            'rests' => [
                [
                    'start_time' => '12:00',
                    'end_time' => '19:00', 
                ]
            ],
            'reason' => '修正理由',
        ]);
        $response->assertSessionHasErrors('rests');

        $response = $this->actingAs($user)->post(route('stamp_correction_request.store'), [
            'attendance_id' => $attendance->id,
            'start_time' => '09:00',
            'end_time' => '18:00',
            'rests' => [
                [
                    'start_time' => '13:00', 
                    'end_time' => '12:00', 
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
            'is_approved' => false, 
        ]);

        $response = $this->actingAs($user)->get(route('stamp_correction_request.index'));
        $response->assertSee('承認待ち');
        $response->assertSee('詳細');
    }
}