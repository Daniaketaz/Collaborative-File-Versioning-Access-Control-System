<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignNewUsersToGroupRequest extends FormRequest
{
    public function authorize()
    {
        // تحقق ما إذا كان المستخدم الحالي لديه دور "admin"
        return auth()->user() ;
    }

    public function rules()
    {
        return [
            'group_id' => 'required|integer|exists:groups,id',
            'user_ids' => 'required|array',
            'user_ids.*' => 'integer|exists:users,id',
        ];
    }

    public function messages()
    {
        return [
            'group_id.required' => 'حقل المجموعة مطلوب.',
            'group_id.integer' => 'يجب أن يكون معرف المجموعة رقميًا.',
            'group_id.exists' => 'المجموعة المحددة غير موجودة.',
            'user_ids.required' => 'يجب تحديد المستخدمين.',
            'user_ids.array' => 'يجب أن يكون المستخدمون في شكل مصفوفة.',
            'user_ids.*.exists' => 'المستخدم المحدد غير موجود.',
        ];
    }
}
