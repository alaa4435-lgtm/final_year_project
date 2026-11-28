<?php

return [
    'required' => 'حقل :attribute مطلوب.',
    'email' => ':attribute يجب أن يكون بريداً إلكترونياً صحيحاً.',
    'min' => [
        'array' => ':attribute يجب أن يحتوي على :min عناصر على الأقل.',
        'file' => 'حجم :attribute يجب أن يكون :min كيلوبايت على الأقل.',
        'numeric' => ':attribute يجب أن يكون :min على الأقل.',
        'string' => ':attribute يجب أن يكون :min أحرف على الأقل.',
    ],
    'max' => [
        'array' => ':attribute يجب ألا يحتوي على أكثر من :max عناصر.',
        'file' => 'حجم :attribute يجب ألا يتجاوز :max كيلوبايت.',
        'numeric' => ':attribute يجب ألا يتجاوز :max.',
        'string' => ':attribute يجب ألا يتجاوز :max أحرف.',
    ],
    'confirmed' => 'تأكيد :attribute غير مطابق.',
    'unique' => ':attribute مستخدم من قبل.',
    'password_invalid' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.',
];
