<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
        [   
            'name' => '管理者太郎',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 1, // ★ここが重要！1なら管理者
        ],
        [   
            'name' => '西怜奈',
            'email' => 'reina.n@coachtech.com',
            'password' => Hash::make('password'),
            'role' => 0 // ★ここが重要！1なら管理者
        ],
        [   
            'name' => '山田太郎',
            'email' => 'taro.y@coachtech.com',
            'password' => Hash::make('password'),
            'role' => 0 // ★ここが重要！1なら管理者
        ],
        [   
            'name' => '増田一世',
            'email' => 'issei.m@coachtech.com',
            'password' => Hash::make('password'),
            'role' => 0 // ★ここが重要！1なら管理者
        ],
        [   
            'name' => '山本敬吉',
            'email' => 'keikichi.y@coachtech.com',
            'password' => Hash::make('password'),
            'role' => 0 // ★ここが重要！1なら管理者
        ],
        [   
            'name' => '秋田朋美',
            'email' => 'tomomi.a@coachtech.com',
            'password' => Hash::make('password'),
            'role' => 0 // ★ここが重要！1なら管理者
        ],
        [   
            'name' => '中西教夫',
            'email' => 'norio.n@coachtech.com',
            'password' => Hash::make('password'),
            'role' => 0 // ★ここが重要！1なら管理者
        ],
        ];
        foreach ($users as $user) {
            User::create($user);
        }
    }
}
