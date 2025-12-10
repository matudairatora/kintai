<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StampCorrectionRequestRest extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'stamp_correction_request_id',
        'start_time',
        'end_time',
    ];

   
    public function stamp_correction_request()
    {
        return $this->belongsTo(StampCorrectionRequest::class);
    }
}
