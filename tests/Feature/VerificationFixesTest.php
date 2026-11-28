<?php

use App\Models\Template;
use App\Models\User;
use App\Services\ProjectGeneratorService;
use Database\Seeders\StageSeeder;
use Database\Seeders\TemplateSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->withoutVite();
});

function verifiedUser(string $role = 'learner'): User
{
    $user = User::factory()->create(['email_verified_at' => now()]);
    $user->forceFill(['role' => $role, 'status' => 'active'])->save();

    return $user;
}

it('shows a translated size message instead of a raw validation key', function (string $locale, string $expected) {
    $this->withSession(['locale' => $locale])
        ->post(route('register'), [
            'name' => 'Short Pass',
            'email' => 'short@example.com',
            'password' => 'abc',
            'password_confirmation' => 'abc',
        ])
        ->assertSessionHasErrors('password');

    $message = session('errors')->first('password');

    expect($message)->not->toContain('validation.')
        ->and($message)->toContain($expected);
})->with([
    'english' => ['en', 'at least 8 characters'],
    'arabic' => ['ar', 'على الأقل'],
]);

it('shows the incorrect password message on the confirm password screen', function () {
    $user = verifiedUser();

    $this->actingAs($user)
        ->post('/confirm-password', ['password' => 'wrong-password'])
        ->assertSessionHasErrors(['password' => __('auth.password')]);

    expect(__('auth.password'))->toBe('The provided password is incorrect.');
});

it('sends each role to its own dashboard', function () {
    expect(verifiedUser('admin')->dashboardUrl())->toBe(route('admin.dashboard'))
        ->and(verifiedUser()->dashboardUrl())->toBe(route('learner.dashboard'));
});

it('lets an admin open the profile page without learner-only links', function () {
    $admin = verifiedUser('admin');

    $this->actingAs($admin)
        ->get(route('profile.edit'))
        ->assertOk()
        ->assertSee(route('admin.dashboard'), false)
        ->assertDontSee(route('learner.dashboard'), false)
        ->assertDontSee(route('projects.index'), false);
});

it('rejects svg avatars and accepts webp avatars', function () {
    Storage::fake('public');
    $user = verifiedUser();
    $payload = ['name' => $user->name, 'email' => $user->email];

    $svg = UploadedFile::fake()->createWithContent('evil.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>');

    $this->actingAs($user)
        ->patch(route('profile.update'), $payload + ['avatar' => $svg])
        ->assertSessionHasErrors('avatar');

    $this->actingAs($user)
        ->patch(route('profile.update'), $payload + ['avatar' => UploadedFile::fake()->image('ok.webp')])
        ->assertSessionHasNoErrors();

    expect($user->fresh()->avatar)->toEndWith('.webp');
});

it('shows project validation messages in the current language', function (string $locale, string $expected) {
    $this->actingAs(verifiedUser())
        ->withSession(['locale' => $locale])
        ->post(route('projects.store'), [])
        ->assertSessionHasErrors(['title' => $expected]);
})->with([
    'english' => ['en', 'Enter a project name.'],
    'arabic' => ['ar', 'اكتب اسماً للمشروع.'],
]);

it('shows translated stage titles on the project page', function () {
    $this->seed(TemplateSeeder::class);
    $this->seed(StageSeeder::class);
    $user = verifiedUser();
    $project = app(ProjectGeneratorService::class)
        ->generate($user, Template::where('slug', 'portfolio')->first(), 'My Site');

    $this->actingAs($user)
        ->withSession(['locale' => 'ar'])
        ->get(route('projects.show', $project))
        ->assertOk()
        ->assertSeeText(['مقدمة في تطوير الويب', 'شريط التنقل', 'تذييل الصفحة'])
        ->assertDontSeeText('Navigation Bar');
});

it('keeps login and register guest-only', function (string $routeName) {
    $this->actingAs(verifiedUser())
        ->get(route($routeName))
        ->assertRedirect();
})->with(['login', 'register']);
