<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StampCorrectionRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'attendance_id',
        'reason',
        'is_approved',
        'status',          
        'new_start_time',  
        'new_end_time', 
    ];

    // ユーザーとの関係
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 勤怠との関係
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}
