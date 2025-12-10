<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\StampCorrectionRequest;
use Carbon\Carbon;

class AdminAttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected function createAdmin()
    {
        return User::factory()->create(['role' => 1]); // role 1 = 管理者
    }

    /**
     * ID: 12 勤怠一覧取得機能（管理者）
     */
    public function test_admin_can_view_daily_attendance_list()
    {
        $admin = $this->createAdmin();
        $user = User::factory()->create(['name' => 'General User']);
        
        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::today()->format('Y-m-d'), 
        ]);

        $response = $this->actingAs($admin)->get(route('admin.attendance.list'));
        
        $response->assertStatus(200);
        $response->assertSee(Carbon::today()->format('Y年n月j日'));
        $response->assertSee('General User');
        $response->assertSee('09:00');
        
        $response->assertSee('前日');
        $response->assertSee('翌日');
    }

    /**
     * ID: 13 勤怠詳細取得・修正機能（管理者） - 正常系
     */
    public function test_admin_can_update_attendance_directly()
    {
        $admin = $this->createAdmin();
        $user = User::factory()->create();
    
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::today()->format('Y-m-d'), 
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.attendance.show', $attendance->id));
        $response->assertStatus(200);
        $response->assertSee('09:00');

        $response = $this->actingAs($admin)->patch(route('admin.attendance.update', $attendance->id), [
            'start_time' => '10:00',
            'end_time' => '19:00',
            'reason' => '管理者による修正',
        ]);

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'start_time' => '10:00:00',
            'end_time' => '19:00:00',
        ]);
    }

    /**
     * ID: 13 勤怠詳細取得・修正機能（管理者） - バリデーションエラー系
     */
    public function test_admin_update_validation_errors()
    {
        $admin = $this->createAdmin();
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        // 1. 出勤時間 > 退勤時間
        $response = $this->actingAs($admin)->patch(route('admin.attendance.update', $attendance->id), [
            'start_time' => '19:00',
            'end_time' => '18:00',
            'reason' => '修正',
        ]);
        $response->assertSessionHasErrors(['start_time']);

        // 2. 休憩時間が勤務時間外
        $response = $this->actingAs($admin)->patch(route('admin.attendance.update', $attendance->id), [
            'start_time' => '09:00',
            'end_time' => '18:00',
            'rests' => [
                [
                    'start_time' => '19:00',
                    'end_time' => '19:30',
                ]
            ],
            'reason' => '修正',
        ]);
        $response->assertSessionHasErrors('rests');

        // 3. 備考未入力
        $response = $this->actingAs($admin)->patch(route('admin.attendance.update', $attendance->id), [
            'start_time' => '09:00',
            'end_time' => '18:00',
            'reason' => '',
        ]);
        $response->assertSessionHasErrors('reason');
    }

    /**
     * ID: 14 ユーザー情報取得機能（管理者）
     */
    public function test_admin_can_view_staff_list()
    {
        $admin = $this->createAdmin();
        User::factory()->create(['name' => 'Staff One', 'role' => 0]); 
        User::factory()->create(['name' => 'Staff Two', 'role' => 0]);

        $response = $this->actingAs($admin)->get(route('admin.staff.list'));
        
        $response->assertStatus(200);
        $response->assertSee('Staff One');
        $response->assertSee('Staff Two');
        $response->assertSee('詳細');
    }

    /**
     * ID: 15 勤怠情報修正機能（承認プロセス）
     */
    public function test_admin_can_approve_correction_request()
    {
        $admin = $this->createAdmin();
        $user = User::factory()->create();
        
        // 日付を指定して作成
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::today()->format('Y-m-d'), 
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        $request = StampCorrectionRequest::create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'new_start_time' => '10:00',
            'new_end_time' => '19:00',
            'reason' => '遅刻修正',
            'is_approved' => false,
            'status' => '承認待ち', 
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.stamp_correction_request.list'));
        $response->assertSee('承認待ち');
        $response->assertSee('遅刻修正');

        $response = $this->actingAs($admin)->get(route('admin.stamp_correction_request.show', $request->id));
        $response->assertSee('承認');

        $this->actingAs($admin)->get(route('admin.stamp_correction_request.approve', $request->id));

        $this->assertDatabaseHas('stamp_correction_requests', [
            'id' => $request->id,
            'is_approved' => true,
        ]);

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'start_time' => '10:00:00',
        ]);
    }
}