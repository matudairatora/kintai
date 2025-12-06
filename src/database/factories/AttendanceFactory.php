<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Attendance;

class AttendanceFactory extends Factory
{
    
    protected $model = Attendance::class;
    
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'date' => $this->faker->date(),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ];
    }
}
