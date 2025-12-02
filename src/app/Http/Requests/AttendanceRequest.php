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
            
            // 休憩時間の取得（フォームの実装に合わせて調整が必要）
            // ここでは単一の休憩入力がある前提の例です
            $breakStart = isset($data['break_start']) ? Carbon::parse($data['break_start']) : null;
            $breakEnd = isset($data['break_end']) ? Carbon::parse($data['break_end']) : null;

            // 1. 出勤・退勤の矛盾チェック
            if ($start && $end && $start->gt($end)) {
                 $validator->errors()->add('start_time', '出勤時間もしくは退勤時間が不適切な値です');
            }

            // 2. 休憩開始時間のチェック（出勤より前、または退勤より後）
            if ($breakStart) {
                if (($start && $breakStart->lt($start)) || ($end && $breakStart->gt($end))) {
                    $validator->errors()->add('break_start', '休憩時間が不適切な値です');
                }
            }

            // 3. 休憩終了時間のチェック（退勤より後）
            if ($breakEnd) {
                if ($end && $breakEnd->gt($end)) {
                    $validator->errors()->add('break_end', '休憩時間もしくは退勤時間が不適切な値です');
                }
                // 休憩終了が開始より前の場合も不適切
                if ($breakStart && $breakEnd->lt($breakStart)) {
                     $validator->errors()->add('break_end', '休憩時間が不適切な値です');
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
            'reason.required' => '備考を記入してください',
        ];
    }

}
