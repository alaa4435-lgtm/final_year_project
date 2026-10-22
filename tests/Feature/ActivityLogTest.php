<?php

namespace Tests\Feature;

use App\ActivityAction;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_creates_an_activity_log(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test Learner',
            'email' => 'learner@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect();

        $user = User::where('email', 'learner@example.com')->firstOrFail();

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'action' => ActivityAction::UserRegistered->value,
        ]);
    }

    public function test_successful_login_creates_an_activity_log(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'action' => ActivityAction::UserLogin->value,
        ]);
    }

    public function test_failed_login_does_not_create_a_login_activity_log(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertDatabaseMissing('activity_logs', [
            'user_id' => $user->id,
            'action' => ActivityAction::UserLogin->value,
        ]);
    }

    public function test_logout_creates_an_activity_log(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect();

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'action' => ActivityAction::UserLogout->value,
        ]);
    }

    public function test_email_verification_creates_an_activity_log(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect();
        $this->assertTrue($user->fresh()->hasVerifiedEmail());

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'action' => ActivityAction::EmailVerified->value,
        ]);
    }

    public function test_password_reset_creates_an_activity_log(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $token = Password::createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'action' => ActivityAction::PasswordReset->value,
        ]);
    }

    public function test_activity_logs_never_store_passwords_or_tokens(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);
        $token = Password::createToken($user);
        $plainPassword = 'new-password123';

        // Trigger all 5 logged events.
        $this->post('/register', [
            'name' => 'Another Learner',
            'email' => 'another@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->post('/login', ['email' => $user->email, 'password' => 'password123']);
        $this->actingAs($user)->post('/logout');

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );
        $this->actingAs($user)->get($verificationUrl);

        $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => $plainPassword,
            'password_confirmation' => $plainPassword,
        ]);

        // 1) The activity_logs table has no columns capable of holding credentials.
        $columns = Schema::getColumnListing('activity_logs');
        $this->assertEqualsCanonicalizing(
            ['id', 'user_id', 'action', 'description', 'ip_address', 'user_agent', 'created_at', 'updated_at'],
            $columns
        );

        // 2) None of the recorded text actually contains the raw password, token, or password hash.
        $allLoggedText = ActivityLog::query()
            ->pluck('description')
            ->filter()
            ->implode(' | ');

        $this->assertStringNotContainsString($plainPassword, $allLoggedText);
        $this->assertStringNotContainsString($token, $allLoggedText);
        $this->assertStringNotContainsString($user->fresh()->password, $allLoggedText);
        $this->assertStringNotContainsString('password123', $allLoggedText);
    }
}
