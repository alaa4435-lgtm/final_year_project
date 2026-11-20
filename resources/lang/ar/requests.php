<?php

return [
    'store_project' => [
        'template_id.required' => 'اختر نوع الموقع أولاً.',
        'template_id.integer' => 'نوع الموقع المختار غير صحيح.',
        'template_id.exists' => 'القالب المختار غير متاح.',
        'title.required' => 'اكتب اسماً للمشروع.',
        'title.string' => 'اسم المشروع يجب أن يكون نصاً.',
        'title.max' => 'اسم المشروع يجب ألا يتجاوز 255 حرفاً.',
    ],

    'update_profile' => [
        'email.unique' => 'هذا البريد الإلكتروني مستخدم بالفعل.',
        'avatar.image' => 'الملف يجب أن يكون صورة.',
        'avatar.mimes' => 'الصورة يجب أن تكون JPG أو PNG.',
        'avatar.max' => 'حجم الصورة يجب ألا يتجاوز 2 ميغابايت.',
        'current_password.required_with' => 'أدخل كلمة المرور الحالية لتغييرها.',
        'current_password.current_password' => 'كلمة المرور الحالية غير صحيحة.',
    ],
];
