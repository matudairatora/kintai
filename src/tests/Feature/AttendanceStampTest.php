<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceStampTest extends TestCase
{
   use RefreshDatabase;

    /**
     * ID: 4 日時取得機能
     */
    public function test_display_current_date_time()
    {
        $user = User::factory()->create();
        
        // 日時を固定
        $now = Carbon::create(2025, 12, 6, 10, 0, 0);
        Carbon::setTestNow($now);

        $response = $this->actingAs($user)->get(route('attendance.index'));

        // 日付と時間が画面にあるか
        $response->assertSee($now->format('H:i'));
        // 年月日は改行が含まれる、部分一致で確認
        $response->assertSee($now->format('Y年'));
        $response->assertSee($now->format('m月d日'));
    }

    /**
     * ID: 5 ステータス確認機能 & ID: 6,7,8 打刻機能
     */
    public function test_status_and_buttons_display_correctly()
    {
        $user = User::factory()->create();
        Carbon::setTestNow(Carbon::create(2025, 12, 6, 9, 0, 0));

        // 1. 勤務外（初期状態）
        $response = $this->actingAs($user)->get(route('attendance.index'));
        $response->assertSee('勤務外');
        // ボタンのvalue属性でチェック（誤検知防止）
        $response->assertSee('value="clock_in"', false); 
        $response->assertDontSee('value="clock_out"', false);
        $response->assertDontSee('value="break_start"', false);

        // 2. 出勤処理
        $this->actingAs($user)->post(route('attendance.store'), ['type' => 'clock_in']);
        
        // 出勤中ステータス確認
        $response = $this->actingAs($user)->get(route('attendance.index'));
        $response->assertSee('出勤中');
        
        // 出勤ボタン（value="clock_in"）が存在しないことを確認
        $response->assertDontSee('value="clock_in"', false);
        
        // 退勤・休憩入ボタンがあるか
        $response->assertSee('value="clock_out"', false);
        $response->assertSee('value="break_start"', false);

        // 休憩入処理
        $this->actingAs($user)->post(route('attendance.store'), ['type' => 'break_start']);

        // 休憩中ステータス確認
        $response = $this->actingAs($user)->get(route('attendance.index'));
        $response->assertSee('休憩中');
        $response->assertSee('value="break_end"', false);
        $response->assertDontSee('value="clock_out"', false);

        // 休憩戻処理
        $this->actingAs($user)->post(route('attendance.store'), ['type' => 'break_end']);

        // 再休憩入処理
        $this->actingAs($user)->post(route('attendance.store'), ['type' => 'break_start']);

        // 休憩中ステータス確認
        $response = $this->actingAs($user)->get(route('attendance.index'));
        $response->assertSee('休憩中');
        $response->assertSee('value="break_end"', false);
        $response->assertDontSee('value="clock_out"', false);

        // 再休憩戻処理
        $this->actingAs($user)->post(route('attendance.store'), ['type' => 'break_end']);


        // 再度出勤中ステータス確認
        $response = $this->actingAs($user)->get(route('attendance.index'));
        $response->assertSee('出勤中');
        
        // 5. 退勤処理
        $this->actingAs($user)->post(route('attendance.store'), ['type' => 'clock_out']);

        // 退勤済ステータス確認
        $response = $this->actingAs($user)->get(route('attendance.index'));
        $response->assertSee('退勤済');
        // 出勤ボタンが再度表示されないこと
        $response->assertDontSee('value="clock_in"', false); 
    }

    /**
     * ID: 6,7,8 打刻時刻が勤怠一覧に反映されるか
     */
    public function test_attendance_records_are_saved_correctly()
    {
        $user = User::factory()->create();
        $date = Carbon::create(2025, 12, 6);
        Carbon::setTestNow($date->copy()->setTime(9, 0));

        // 出勤
        $this->actingAs($user)->post(route('attendance.store'), ['type' => 'clock_in']);

        // 時間を進める（休憩開始）
        Carbon::setTestNow($date->copy()->setTime(12, 0));
        $this->actingAs($user)->post(route('attendance.store'), ['type' => 'break_start']);

        // 時間を進める（休憩終了）
        Carbon::setTestNow($date->copy()->setTime(13, 0));
        $this->actingAs($user)->post(route('attendance.store'), ['type' => 'break_end']);

        // 時間を進める（退勤）
        Carbon::setTestNow($date->copy()->setTime(18, 0));
        $this->actingAs($user)->post(route('attendance.store'), ['type' => 'clock_out']);

        // 勤怠一覧画面を確認
        $response = $this->actingAs($user)->get(route('attendance.list'));
        
        $response->assertSee('09:00'); // 出勤時間
        $response->assertSee('18:00'); // 退勤時間
    }
}
