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
        'date',         
        'start_time',   
        'end_time',     
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
        
        return gmdate('H:i', $totalSeconds);
    }

    public function getTotalWorkTimeAttribute()
    {
        if (!$this->end_time) {
            return null;
        }

        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);
        
    
        $staySeconds = $start->diffInSeconds($end);

        $restSeconds = 0;
        foreach ($this->rests as $rest) {
            if ($rest->start_time && $rest->end_time) {
                $restStart = Carbon::parse($rest->start_time);
                $restEnd = Carbon::parse($rest->end_time);
                $restSeconds += $restStart->diffInSeconds($restEnd);
            }
        }

        return gmdate('H:i', $staySeconds - $restSeconds);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rests()
    {
        return $this->hasMany(Rest::class);
    }
}