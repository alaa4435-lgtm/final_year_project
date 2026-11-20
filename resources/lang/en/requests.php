<?php

return [
    'store_project' => [
        'template_id.required' => 'Choose a website type first.',
        'template_id.integer' => 'The selected website type is invalid.',
        'template_id.exists' => 'The selected template is not available.',
        'title.required' => 'Enter a project name.',
        'title.string' => 'The project name must be text.',
        'title.max' => 'The project name must not exceed 255 characters.',
    ],

    'update_profile' => [
        'email.unique' => 'This email is already in use.',
        'avatar.image' => 'The file must be an image.',
        'avatar.mimes' => 'The image must be a JPG or PNG.',
        'avatar.max' => 'The image must not exceed 2 MB.',
        'current_password.required_with' => 'Enter your current password to change it.',
        'current_password.current_password' => 'The current password is incorrect.',
    ],
];
