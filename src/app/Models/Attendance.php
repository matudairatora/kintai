<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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