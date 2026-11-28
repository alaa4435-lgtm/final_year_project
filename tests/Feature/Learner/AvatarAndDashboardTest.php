<?php

use App\Models\Project;
use App\Models\Template;
use App\Models\User;
use App\Services\ProjectGeneratorService;
use Database\Seeders\StageSeeder;
use Database\Seeders\TemplateSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->withoutVite();
    $this->seed(TemplateSeeder::class);
    $this->seed(StageSeeder::class);
    Storage::fake('public');
});

function avatarUser(): User
{
    $user = User::factory()->create(['email_verified_at' => now()]);
    $user->forceFill(['role' => 'learner', 'status' => 'active'])->save();

    return $user;
}

function profilePayload(User $user, array $extra = []): array
{
    return array_merge(['name' => $user->name, 'email' => $user->email], $extra);
}

// ───────────────────────── Avatar ─────────────────────────

it('uploads an avatar and stores it on the public disk', function () {
    $user = avatarUser();

    $this->actingAs($user)
        ->patch(route('profile.update'), profilePayload($user, [
            'avatar' => UploadedFile::fake()->image('me.jpg', 200, 200),
        ]))
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit'));

    $user->refresh();

    expect($user->avatar)->not->toBeNull();
    Storage::disk('public')->assertExists($user->avatar);
});

it('deletes the old avatar file when a new one is uploaded', function () {
    $user = avatarUser();

    $this->actingAs($user)->patch(route('profile.update'), profilePayload($user, [
        'avatar' => UploadedFile::fake()->image('first.jpg'),
    ]));
    $oldPath = $user->fresh()->avatar;

    $this->actingAs($user)->patch(route('profile.update'), profilePayload($user, [
        'avatar' => UploadedFile::fake()->image('second.png'),
    ]));
    $newPath = $user->fresh()->avatar;

    expect($newPath)->not->toBe($oldPath);
    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists($newPath);
});

it('removes the avatar when requested', function () {
    $user = avatarUser();

    $this->actingAs($user)->patch(route('profile.update'), profilePayload($user, [
        'avatar' => UploadedFile::fake()->image('me.jpg'),
    ]));
    $path = $user->fresh()->avatar;

    $this->actingAs($user)->patch(route('profile.update'), profilePayload($user, [
        'remove_avatar' => 1,
    ]));

    expect($user->fresh()->avatar)->toBeNull();
    Storage::disk('public')->assertMissing($path);
});

it('rejects an avatar that is not an image', function () {
    $user = avatarUser();

    $this->actingAs($user)
        ->patch(route('profile.update'), profilePayload($user, [
            'avatar' => UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf'),
        ]))
        ->assertSessionHasErrors('avatar');

    expect($user->fresh()->avatar)->toBeNull();
});

it('accepts an avatar of exactly 5 MB', function () {
    $user = avatarUser();

    $this->actingAs($user)
        ->patch(route('profile.update'), profilePayload($user, [
            'avatar' => UploadedFile::fake()->image('limit.jpg')->size(5120),
        ]))
        ->assertSessionHasNoErrors();

    expect($user->fresh()->avatar)->not->toBeNull();
});

it('rejects an avatar larger than 5 MB', function () {
    $user = avatarUser();

    $this->actingAs($user)
        ->patch(route('profile.update'), profilePayload($user, [
            'avatar' => UploadedFile::fake()->image('big.jpg')->size(5121),
        ]))
        ->assertSessionHasErrors('avatar');
});

it('shows the size limit message when the server rejects the upload', function () {
    $user = avatarUser();
    $path = tempnam(sys_get_temp_dir(), 'avatar');

    $this->actingAs($user)
        ->patch(route('profile.update'), profilePayload($user, [
            'avatar' => new UploadedFile($path, 'big.jpg', 'image/jpeg', UPLOAD_ERR_INI_SIZE, true),
        ]))
        ->assertSessionHasErrors(['avatar' => __('profile.errors.max')]);
});

// ───────────────────────── Dashboard ─────────────────────────

it('shows correct stats for the learner on the dashboard', function () {
    $user = avatarUser();
    $template = Template::active()->first();
    $generator = app(ProjectGeneratorService::class);

    $generator->generate($user, $template, 'Project One');
    $done = $generator->generate($user, $template, 'Project Two');
    $done->update(['status' => 'completed']);

    $response = $this->actingAs($user)
        ->get(route('learner.dashboard'))
        ->assertOk();

    expect($response->viewData('stats'))->toBe([
        'total' => 2,
        'in_progress' => 1,
        'completed' => 1,
    ]);
});

it('does not count other learners projects on the dashboard', function () {
    $user = avatarUser();
    $other = avatarUser();
    $template = Template::active()->first();

    app(ProjectGeneratorService::class)->generate($other, $template, 'Not Mine');

    $response = $this->actingAs($user)
        ->get(route('learner.dashboard'))
        ->assertOk();

    expect($response->viewData('stats')['total'])->toBe(0);
    expect(Project::count())->toBe(1);
});

it('renders the dashboard fully translated', function (string $locale, array $expected) {
    $user = avatarUser();
    app(ProjectGeneratorService::class)->generate($user, Template::where('slug', 'portfolio')->first(), 'My Site');

    $response = $this->actingAs($user)
        ->withSession(['locale' => $locale])
        ->get(route('learner.dashboard'))
        ->assertOk()
        ->assertSeeText($expected)
        ->assertDontSee('learner_dashboard.')
        ->assertDontSee('catalog.templates.')
        ->assertDontSee('project.progress')
        ->assertDontSee('projects.');
})->with([
    'english' => ['en', ['Dashboard', 'Total projects', 'Portfolio Website', 'In progress', 'Continue where you left off']],
    'arabic' => ['ar', ['لوحة التحكم', 'إجمالي المشاريع', 'موقع معرض أعمال', 'قيد التنفيذ', 'أكمل من حيث توقفت']],
]);
