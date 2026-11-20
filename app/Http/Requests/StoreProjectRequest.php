<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        // المستخدم مسجل دخول بفضل middleware auth
        return true;
    }

    public function rules(): array
    {
        return [
            'template_id' => ['required', 'integer', 'exists:templates,id,is_active,1'],
            'title' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'template_id.required' => 'اختر نوع الموقع أولاً.',
            'template_id.integer' => 'نوع الموقع المختار غير صحيح.',
            'template_id.exists' => 'القالب المختار غير متاح.',
            'title.required' => 'اكتب اسماً للمشروع.',
            'title.string' => 'اسم المشروع يجب أن يكون نصاً.',
            'title.max' => 'اسم المشروع يجب ألا يتجاوز 255 حرفاً.',
        ];
    }
}
