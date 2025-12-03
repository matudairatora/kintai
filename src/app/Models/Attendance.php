<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = [
       'user_id',
        'date',         // 日付
        'start_time',   // 出勤時間
        'end_time',     // 退勤時間
        'status',   
    ];
    
    public function getTotalRestTimeAttribute()
    {
        $totalSeconds = 0;
        foreach ($this->rests as $rest) {
            if ($rest->start_time && $rest->end_time) {
                $start = Carbon::parse($rest->start_time);
                $end = Carbon::parse($rest->end_time);
                $totalSeconds += $start->diffInSeconds($end);
            }
        }
        
        return gmdate('H:i:s', $totalSeconds);
    }

    // 勤務時間の合計（退勤 - 出勤 - 休憩合計）
    public function getTotalWorkTimeAttribute()
    {
        if (!$this->end_time) {
            return null;
        }

        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);
        
        // 滞在時間（秒）
        $staySeconds = $start->diffInSeconds($end);

        // 休憩時間（秒）を引く
        $restSeconds = 0;
        foreach ($this->rests as $rest) {
            if ($rest->start_time && $rest->end_time) {
                $restStart = Carbon::parse($rest->start_time);
                $restEnd = Carbon::parse($rest->end_time);
                $restSeconds += $restStart->diffInSeconds($restEnd);
            }
        }

        return gmdate('H:i:s', $staySeconds - $restSeconds);
    }


    // ユーザーとの関係（多対1）
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 休憩との関係（1対多）
    public function rests()
    {
        return $this->hasMany(Rest::class);
    }
}