<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Attendance;
use App\Models\StampCorrectionRequest;

class StampCorrectionRequestFactory extends Factory
{
    protected $model = StampCorrectionRequest::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'attendance_id' => Attendance::factory(),
            'new_start_time' => '09:00:00',
            'new_end_time' => '18:00:00',
            'reason' => $this->faker->realText(20),
            'is_approved' => false,
            'requested_at' => now(),
        ];
    }
}
