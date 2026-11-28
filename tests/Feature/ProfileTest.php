<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('Test User', $user->name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertNull($user->email_verified_at);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($user->fresh());
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('userDeletion', 'password')
        ->assertRedirect('/profile');

    $this->assertNotNull($user->fresh());
});

test('user can upload an avatar', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => $user->email,
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertNotNull($user->refresh()->avatar);
    Storage::disk('public')->assertExists($user->avatar);
});

test('failed avatar upload shows an upload error instead of the size error', function () {
    $user = User::factory()->create();
    $path = tempnam(sys_get_temp_dir(), 'avatar');
    file_put_contents($path, 'partial');
    $failedUpload = new UploadedFile($path, 'avatar.jpg', 'image/jpeg', UPLOAD_ERR_PARTIAL, true);

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => $user->email,
            'avatar' => $failedUpload,
        ]);

    $response->assertSessionHasErrors('avatar');
    $message = session('errors')->first('avatar');

    expect($message)
        ->toContain(__('profile.errors.uploaded'))
        ->not->toBe(__('profile.errors.max'));
});

test('profile page is fully translated', function (string $locale, array $expected) {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['locale' => $locale])
        ->get(route('profile.edit'))
        ->assertOk()
        ->assertSeeText($expected)
        ->assertDontSee('profile.')
        ->assertDontSee('auth.forgot_password');
})->with([
    'english' => ['en', ['Settings', 'Manage your account details, password and preferences.', 'Update your personal details and profile picture.', 'Change your account password.', 'Forgot Your Password?']],
    'arabic' => ['ar', ['الإعدادات', 'إدارة تفاصيل حسابك وكلمة المرور وتفضيلاتك.', 'تحديث بياناتك الشخصية وصورة ملفك الشخصي.', 'غيّر كلمة مرور حسابك.', 'نسيت كلمة المرور؟']],
]);

test('profile page shows a translated message after saving', function (string $locale, string $message) {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['locale' => $locale])
        ->patch(route('profile.update'), ['name' => 'New Name', 'email' => $user->email])
        ->assertRedirect(route('profile.edit'));

    $this->actingAs($user)
        ->get(route('profile.edit'))
        ->assertSeeText($message);
})->with([
    'english' => ['en', 'Your profile has been updated.'],
    'arabic' => ['ar', 'تم تحديث ملفك الشخصي.'],
]);

test('profile updated message auto-dismisses after 3 seconds', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['status' => 'profile-updated'])
        ->get('/profile')
        ->assertOk()
        ->assertSee(__('profile.flash.profile-updated'))
        ->assertSee('data-auto-dismiss="3000"', false)
        ->assertSee('setTimeout(() => show = false, 3000)', false);
});
