<?php

use App\Models\User;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;

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

    $abstractUser = new class implements SocialiteUser
    {
        public array $user = ['email_verified' => true];

        public function getId(): string
        {
            return '123456';
        }

        public function getName(): string
        {
            return 'Test User';
        }

        public function getEmail(): string
        {
            return 'test@example.com';
        }

        public function getAvatar(): ?string
        {
            return null;
        }

        public function getToken(): string
        {
            return 'google-token';
        }

        public function getRefreshToken(): string
        {
            return 'google-refresh-token';
        }

        public function getExpiresIn(): ?int
        {
            return null;
        }

        public function getNickname(): ?string
        {
            return null;
        }

        public function getUser(): array
        {
            return $this->user;
        }
    };

    Socialite::fake('google', $abstractUser);

    $response = $this->get('/auth/google/callback');

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('dashboard'));
});

test('google callback links existing email user without changing status', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'google_id' => null,
        'status' => 'inactive',
    ]);

    $abstractUser = new class implements SocialiteUser
    {
        public array $user = ['email_verified' => true];

        public function getId(): string
        {
            return '123456';
        }

        public function getName(): string
        {
            return 'Test User';
        }

        public function getEmail(): string
        {
            return 'test@example.com';
        }

        public function getAvatar(): ?string
        {
            return null;
        }

        public function getToken(): string
        {
            return 'google-token';
        }

        public function getRefreshToken(): string
        {
            return 'google-refresh-token';
        }

        public function getExpiresIn(): ?int
        {
            return null;
        }

        public function getNickname(): ?string
        {
            return null;
        }

        public function getUser(): array
        {
            return $this->user;
        }
    };

    Socialite::fake('google', $abstractUser);

    $response = $this->get('/auth/google/callback');

    $this->assertAuthenticatedAs($user);

    $user->refresh();
    expect($user->google_id)->toBe('123456');
    expect($user->status)->toBe('inactive'); // Status unchanged per controller logic
    expect($user->email_verified_at)->not->toBeNull();

    $response->assertRedirect(route('dashboard'));
});

test('google callback registers a new user and activates them', function () {
    $abstractUser = new class implements SocialiteUser
    {
        public array $user = ['email_verified' => true];

        public function getId(): string
        {
            return '123456';
        }

        public function getName(): string
        {
            return 'Test User';
        }

        public function getEmail(): string
        {
            return 'test@example.com';
        }

        public function getAvatar(): ?string
        {
            return null;
        }

        public function getToken(): string
        {
            return 'google-token';
        }

        public function getRefreshToken(): string
        {
            return 'google-refresh-token';
        }

        public function getExpiresIn(): ?int
        {
            return null;
        }

        public function getNickname(): ?string
        {
            return null;
        }

        public function getUser(): array
        {
            return $this->user;
        }
    };

    Socialite::fake('google', $abstractUser);

    $response = $this->get('/auth/google/callback');

    $this->assertAuthenticated();

    $user = User::where('email', 'test@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->google_id)->toBe('123456');
    expect($user->status)->toBe('active');
    expect($user->role)->toBe('learner');

    $response->assertRedirect(route('dashboard'));
});
