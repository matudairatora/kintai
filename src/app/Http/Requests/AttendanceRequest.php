<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class AttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'break_start' => 'nullable', 
            'break_end' => 'nullable',
            'reason' => 'required',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $this->all();
            
            // 時間の取得とCarbon変換
            $start = isset($data['start_time']) ? Carbon::parse($data['start_time']) : null;
            $end = isset($data['end_time']) ? Carbon::parse($data['end_time']) : null;
            
            // 休憩時間の取得
            $breakStart = isset($data['break_start']) ? Carbon::parse($data['break_start']) : null;
            $breakEnd = isset($data['break_end']) ? Carbon::parse($data['break_end']) : null;

            // 1. 出勤・退勤の矛盾チェック
            if ($start && $end && $start->gt($end)) {
                 $validator->errors()->add('start_time', '出勤時間もしくは退勤時間が不適切な値です');
            }

           // 2. 休憩時間のチェック
            if (isset($data['rests']) && is_array($data['rests'])) {
                foreach ($data['rests'] as $id => $restData) {
                    $restStart = isset($restData['start_time']) && $restData['start_time'] ? Carbon::parse($restData['start_time']) : null;
                    $restEnd = isset($restData['end_time']) && $restData['end_time'] ? Carbon::parse($restData['end_time']) : null;

                    // 休憩開始と終了の整合性 (開始 > 終了 になっていないか)
                    if ($restStart && $restEnd && $restStart->gt($restEnd)) {
                        $validator->errors()->add('rests', '休憩開始時間が休憩終了時間より後になっています。');
                    }

                    // 勤務時間との整合性 (休憩開始 < 出勤)
                    if ($start && $restStart && $restStart->lt($start)) {
                        $validator->errors()->add('rests', '休憩時間が勤務時間外です。');
                    }

                    // 勤務時間との整合性 (休憩終了 > 退勤)
                    if ($end && $restEnd && $restEnd->gt($end)) {
                        $validator->errors()->add('rests', '休憩時間が勤務時間外です。');
                    }
                }
            }

        });
    }

    public function messages()
    {
        return [
            'start_time.required' => '出勤時間を入力してください',
            'end_time.required' => '退勤時間を入力してください',
            'end_time.after' => '出勤時間もしくは退勤時間が不適切な値です',
            'reason.required' => '備考を記入してください'
        ];
    }

}
