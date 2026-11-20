<?php

use App\ActivityAction;
use App\Models\ActivityLog;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

uses(RefreshDatabase::class);

// ───────────────────────── Helpers ─────────────────────────

function p1Password(): string
{
    return 'Secret#12345';
}

/**
 * Create a user with safe defaults. role, status and google_id are not
 * mass-assignable, so they are set with forceFill.
 */
function p1User(array $attributes = []): User
{
    $user = User::factory()->create([
        'password' => p1Password(),
        'email_verified_at' => now(),
    ]);

    $user->forceFill(array_merge([
        'role' => 'learner',
        'status' => 'active',
    ], $attributes))->save();

    return $user;
}

/**
 * Count log rows of one action for one specific user.
 */
function p1LogCount(ActivityAction $action, ?User $user = null): int
{
    $query = ActivityLog::where('action', $action->value);

    if ($user) {
        $query->where('user_id', $user->id);
    }

    return $query->count();
}

/**
 * Mock Socialite so no real Google request is made.
 */
function p1FakeGoogle(array $data = []): void
{
    $googleUser = (new SocialiteUser)->map([
        'id' => $data['id'] ?? 'google-123',
        'name' => $data['name'] ?? 'Google Person',
        'email' => $data['email'] ?? 'google@example.com',
        'nickname' => null,
        'avatar' => null,
    ]);

    // The controller reads $googleUser->user['email_verified']
    $googleUser->setRaw(['email_verified' => $data['verified'] ?? true]);

    Socialite::shouldReceive('driver')->with('google')->andReturnSelf();
    Socialite::shouldReceive('user')->andReturn($googleUser);
}

// ───────────────────────── Seeder ─────────────────────────

it('seeds an active admin account', function () {
    $this->seed(AdminSeeder::class);

    $admin = User::where('email', 'admin@aurie.test')->first();

    expect($admin)->not->toBeNull()
        ->and($admin->role)->toBe('admin')
        ->and($admin->status)->toBe('active');
});

// ───────────────────────── Registration ─────────────────────────

it('registers an active learner and logs the registration', function () {
    $this->post('/register', [
        'name' => 'New Learner',
        'email' => 'new@example.com',
        'password' => p1Password(),
        'password_confirmation' => p1Password(),
    ])->assertRedirect(url('/dashboard'));

    $user = User::where('email', 'new@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->role)->toBe('learner')
        ->and($user->status)->toBe('active')
        ->and(Hash::check(p1Password(), $user->password))->toBeTrue()
        ->and(p1LogCount(ActivityAction::UserRegistered, $user))->toBe(1);
});

it('does not let the request set role or status', function () {
    $this->post('/register', [
        'name' => 'Sneaky',
        'email' => 'sneaky@example.com',
        'password' => p1Password(),
        'password_confirmation' => p1Password(),
        'role' => 'admin',
        'status' => 'suspended',
    ]);

    $user = User::where('email', 'sneaky@example.com')->first();

    expect($user->role)->toBe('learner')
        ->and($user->status)->toBe('active');
});

it('rejects registration with a mismatched password confirmation', function () {
    $this->post('/register', [
        'name' => 'Typo',
        'email' => 'typo@example.com',
        'password' => p1Password(),
        'password_confirmation' => 'different',
    ])->assertSessionHasErrors('password');

    $this->assertDatabaseMissing('users', ['email' => 'typo@example.com']);
});

// ───────────────────────── Login / logout ─────────────────────────

it('sends a learner to the learner dashboard after login', function () {
    $user = p1User(['role' => 'learner']);

    $this->post('/login', [
        'email' => $user->email,
        'password' => p1Password(),
    ])->assertRedirect(route('learner.dashboard'));

    $this->assertAuthenticatedAs($user);
    expect(p1LogCount(ActivityAction::UserLogin, $user))->toBe(1);
});

it('sends an admin to the admin dashboard after login', function () {
    $admin = p1User(['role' => 'admin']);

    $this->post('/login', [
        'email' => $admin->email,
        'password' => p1Password(),
    ])->assertRedirect(route('admin.dashboard'));
});

it('rejects a wrong password without logging a login', function () {
    $user = p1User();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
    expect(p1LogCount(ActivityAction::UserLogin, $user))->toBe(0);
});

it('blocks a suspended user at login', function () {
    $user = p1User(['status' => 'suspended']);

    $this->post('/login', [
        'email' => $user->email,
        'password' => p1Password(),
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
    expect(p1LogCount(ActivityAction::UserLogin, $user))->toBe(0);
});

it('logs out and records the logout', function () {
    $user = p1User();

    $this->actingAs($user)->post('/logout')->assertRedirect('/');

    $this->assertGuest();
    expect(p1LogCount(ActivityAction::UserLogout, $user))->toBe(1);
});

// ───────────────────────── Email verification ─────────────────────────

it('sends an unverified user to the verify-email page', function () {
    $user = p1User(['email_verified_at' => null]);

    $this->actingAs($user)->get('/dashboard')
        ->assertRedirect(route('verification.notice'));
});

it('verifies the email through a signed link', function () {
    $user = p1User(['email_verified_at' => null]);

    $url = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $this->actingAs($user)->get($url)->assertRedirect();

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

// ───────────────────────── Password reset ─────────────────────────

it('sends a reset link and changes the password with it', function () {
    Notification::fake();

    $user = p1User(['email' => 'reset@example.com']);

    $this->post('/forgot-password', ['email' => $user->email])
        ->assertSessionHas('status');

    $token = null;
    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use (&$token) {
        $token = $notification->token;

        return true;
    });

    $this->post('/reset-password', [
        'token' => $token,
        'email' => $user->email,
        'password' => 'NewPass#999',
        'password_confirmation' => 'NewPass#999',
    ])->assertSessionHasNoErrors()
        ->assertRedirectToRoute('login');

    expect(Hash::check('NewPass#999', $user->fresh()->password))->toBeTrue();
});

// ───────────────────────── Access control ─────────────────────────

it('redirects a guest from the dashboard to login', function () {
    $this->get('/dashboard')->assertRedirectToRoute('login');
});

it('blocks a learner from the admin area', function () {
    $learner = p1User(['role' => 'learner']);

    $this->actingAs($learner)->get('/admin')->assertForbidden();
});

it('blocks an admin from the learner area', function () {
    $admin = p1User(['role' => 'admin']);

    $this->actingAs($admin)->get('/learner/dashboard')->assertForbidden();
});

it('lets a learner open the learner dashboard', function () {
    $learner = p1User(['role' => 'learner', 'status' => 'active']);

    $this->actingAs($learner)->get('/learner/dashboard')->assertOk();
});

it('lets an admin open the admin dashboard', function () {
    $admin = p1User(['role' => 'admin', 'status' => 'active']);

    $this->actingAs($admin)->get('/admin')->assertOk();
});

it('blocks a suspended user who is already logged in', function () {
    $user = p1User(['status' => 'suspended']);

    $this->actingAs($user)->get('/dashboard')->assertForbidden();
});

// ───────────────────────── Google sign-in ─────────────────────────

it('creates a verified active learner from a new Google account', function () {
    p1FakeGoogle(['id' => 'gid-1', 'email' => 'g@example.com']);

    $this->get('/auth/google/callback')->assertRedirect(url('/dashboard'));

    $user = User::where('email', 'g@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->google_id)->toBe('gid-1')
        ->and($user->role)->toBe('learner')
        ->and($user->status)->toBe('active')
        ->and($user->email_verified_at)->not->toBeNull()
        ->and(p1LogCount(ActivityAction::UserRegistered, $user))->toBe(1)
        ->and(p1LogCount(ActivityAction::UserLogin, $user))->toBe(1);
});

it('links Google to an existing account without changing its role or status', function () {
    $existing = p1User(['email' => 'linked@example.com', 'role' => 'learner', 'status' => 'active']);

    p1FakeGoogle(['id' => 'gid-2', 'email' => 'linked@example.com']);

    $this->get('/auth/google/callback')->assertRedirect(url('/dashboard'));

    $existing->refresh();

    expect($existing->google_id)->toBe('gid-2')
        ->and($existing->role)->toBe('learner')
        ->and($existing->status)->toBe('active');
});

it('does not log in a suspended account through Google', function () {
    p1User(['email' => 'banned@example.com', 'status' => 'suspended']);

    p1FakeGoogle(['email' => 'banned@example.com']);

    $this->get('/auth/google/callback')
        ->assertRedirectToRoute('login')
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('rejects a Google account whose email is not verified', function () {
    p1FakeGoogle(['email' => 'unverified@example.com', 'verified' => false]);

    $this->get('/auth/google/callback')
        ->assertRedirectToRoute('login')
        ->assertSessionHasErrors('email');

    $this->assertGuest();
    $this->assertDatabaseMissing('users', ['email' => 'unverified@example.com']);
});
