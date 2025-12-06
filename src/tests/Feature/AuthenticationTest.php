<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ID: 1 認証機能（一般ユーザー） - 登録バリデーション
     */
    public function test_register_validation_errors()
    {
        // 名前が未入力
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertSessionHasErrors('name');

        // メールアドレスが未入力
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertSessionHasErrors('email');

        // パスワードが8文字未満
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'pass',
            'password_confirmation' => 'pass',
        ]);
        $response->assertSessionHasErrors('password');

        // パスワード不一致
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password456',
        ]);
        $response->assertSessionHasErrors('password');
    }

    /**
     * ID: 1 認証機能（一般ユーザー） - 正常登録
     */
    public function test_user_can_register_with_valid_data()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'new@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'new@example.com',
        ]);
    }

    /**
     * ID: 2 ログイン認証機能（一般ユーザー）
     */
    public function test_user_login_validation()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);
        $response->assertSessionHasErrors('email');

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);
        $response->assertSessionHasErrors('password');

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
        $response->assertSessionHasErrors('email');
    }

    /**
     * ID: 3 ログイン認証機能（管理者）
     */
    public function test_admin_login_validation()
    {
        $response = $this->post(route('admin.login.submit'), [
            'email' => '',
            'password' => 'password123',
        ]);
        $response->assertSessionHasErrors('email');

        $response = $this->post(route('admin.login.submit'), [
            'email' => 'admin@example.com',
            'password' => '',
        ]);
        $response->assertSessionHasErrors('password');

        $response = $this->post(route('admin.login.submit'), [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);
        $response->assertSessionHasErrors('email');
    }
}
