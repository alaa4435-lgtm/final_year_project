<?php

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('google login screen redirect redirects to google', function () {
    $response = $this->get(route('auth.google'));

    $response->assertRedirectContains('accounts.google.com');
});

test('google callback logs in existing user with google_id', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'google_id' => '123456',
        'status' => 'active',
    ]);

    $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
    $abstractUser->shouldReceive('getId')->andReturn('123456');
    $abstractUser->shouldReceive('getEmail')->andReturn('test@example.com');
    $abstractUser->shouldReceive('getName')->andReturn('Test User');
    $abstractUser->shouldReceive('getToken')->andReturn('google-token');
    $abstractUser->shouldReceive('getRefreshToken')->andReturn('google-refresh-token');

    $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
    $provider->shouldReceive('stateless')->andReturnSelf();
    $provider->shouldReceive('user')->andReturn($abstractUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    $response = $this->get('/auth/google/callback');

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('dashboard'));
});

test('google callback links existing email user and activates them', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'google_id' => null,
        'status' => 'inactive',
    ]);

    $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
    $abstractUser->shouldReceive('getId')->andReturn('123456');
    $abstractUser->shouldReceive('getEmail')->andReturn('test@example.com');
    $abstractUser->shouldReceive('getName')->andReturn('Test User');
    $abstractUser->shouldReceive('getToken')->andReturn('google-token');
    $abstractUser->shouldReceive('getRefreshToken')->andReturn('google-refresh-token');

    $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
    $provider->shouldReceive('stateless')->andReturnSelf();
    $provider->shouldReceive('user')->andReturn($abstractUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    $response = $this->get('/auth/google/callback');

    $this->assertAuthenticatedAs($user);
    
    $user->refresh();
    expect($user->google_id)->toBe('123456');
    expect($user->status)->toBe('active');
    expect($user->email_verified_at)->not->toBeNull();

    $response->assertRedirect(route('dashboard'));
});

test('google callback registers a new user and activates them', function () {
    $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
    $abstractUser->shouldReceive('getId')->andReturn('123456');
    $abstractUser->shouldReceive('getEmail')->andReturn('test@example.com');
    $abstractUser->shouldReceive('getName')->andReturn('Test User');
    $abstractUser->shouldReceive('getToken')->andReturn('google-token');
    $abstractUser->shouldReceive('getRefreshToken')->andReturn('google-refresh-token');

    $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
    $provider->shouldReceive('stateless')->andReturnSelf();
    $provider->shouldReceive('user')->andReturn($abstractUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    $response = $this->get('/auth/google/callback');

    $this->assertAuthenticated();

    $user = User::where('email', 'test@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->google_id)->toBe('123456');
    expect($user->status)->toBe('active');
    expect($user->role)->toBe('learner');

    $response->assertRedirect(route('dashboard'));
});