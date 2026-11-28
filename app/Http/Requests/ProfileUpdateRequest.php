<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'avatar.image' => __('profile.errors.image'),
            'avatar.mimes' => __('profile.errors.mimes'),
            'avatar.max' => __('profile.errors.max'),
            'avatar.uploaded' => $this->avatarUploadFailureMessage(),
        ];
    }

    /**
     * Build a message for an avatar that PHP failed to upload.
     *
     * Size rejections (php.ini or form limit) show the size limit message;
     * other failures show a generic message. PHP's reason is only logged.
     */
    protected function avatarUploadFailureMessage(): string
    {
        $avatar = $this->file('avatar');

        if (! $avatar instanceof UploadedFile || $avatar->isValid()) {
            return __('profile.errors.uploaded');
        }

        Log::warning('Avatar upload failed', [
            'error_code' => $avatar->getError(),
            'reason' => $avatar->getErrorMessage(),
            'client_size' => $avatar->getSize(),
        ]);

        return in_array($avatar->getError(), [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)
            ? __('profile.errors.max')
            : __('profile.errors.uploaded');
    }
}
