<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\VerifyEmail;
use Tests\TestCase;
use App\Models\User;
use App\Providers\RouteServiceProvider;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ID: 16 メール認証機能
     * テスト内容: 会員登録後、認証メールが送信される
     */
    public function test_email_verification_notification_is_sent_upon_registration()
    {
        
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);

        Notification::assertSentTo(
            $user,
            VerifyEmail::class
        );
    }

    /**
     * ID: 16 メール認証機能　メール認証誘導画面の表示確認
     */
    public function test_email_verification_screen_can_be_rendered()
    {
        // メール未認証のユーザーを作成
        $user = User::factory()->unverified()->create();

        // ログイン状態で、認証が必要な「勤怠打刻画面」へアクセス
        $response = $this->actingAs($user)->get(route('attendance.index'));

        // 'verified' ミドルウェアにより、メール認証誘導画面（verification.notice）へリダイレクトされるはず
        $response->assertRedirect(route('verification.notice'));
        
        // 誘導画面が正しく表示されるか確認
        $response = $this->actingAs($user)->get(route('verification.notice'));
        $response->assertStatus(200);
    }

    /**
     * ID: 16 メール認証機能
     * テスト内容: メール認証サイトのメール認証を完了すると、勤怠登録画面に遷移する
     */
    public function test_user_can_verify_email()
    {
        // 未認証ユーザーを作成
        $user = User::factory()->unverified()->create();

        // メール内のリンクを模倣するため、正しい署名付き検証URLを生成
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        // 【検証1】ユーザーの email_verified_at が更新され、認証済みになっているか
        $this->assertTrue($user->fresh()->hasVerifiedEmail());

        // 【検証2】勤怠登録画面（attendance.index）に遷移するか
        $response->assertRedirect(route('attendance.index') . '?verified=1');
    }
}