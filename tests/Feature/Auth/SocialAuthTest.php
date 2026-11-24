<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class SocialAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_github_redirect_stores_state_and_uses_pkce(): void
    {
        config([
            'services.github.client_id' => 'github-client',
            'services.github.client_secret' => 'github-secret',
            'services.github.redirect' => 'http://localhost/auth/github/callback',
        ]);

        $response = $this->get('/auth/github/redirect');

        $response->assertRedirect()
            ->assertSessionHas('oauth_state_github')
            ->assertSessionHas('oauth_verifier_github');

        parse_str((string) parse_url($response->headers->get('Location'), PHP_URL_QUERY), $parameters);
        $verifier = Session::get('oauth_verifier_github');
        $expectedChallenge = rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');

        $this->assertSame('github.com', parse_url($response->headers->get('Location'), PHP_URL_HOST));
        $this->assertSame(Session::get('oauth_state_github'), $parameters['state']);
        $this->assertSame('S256', $parameters['code_challenge_method']);
        $this->assertSame($expectedChallenge, $parameters['code_challenge']);
    }

    public function test_social_redirect_reports_missing_provider_credentials(): void
    {
        config([
            'services.google.client_id' => null,
            'services.google.client_secret' => null,
            'services.google.redirect' => null,
        ]);

        $response = $this->get('/auth/google/redirect');

        $response->assertRedirect('/login')
            ->assertSessionHasErrors(['oauth']);
    }

    public function test_github_callback_creates_a_user_with_a_verified_primary_email(): void
    {
        config([
            'services.github.client_id' => 'github-client',
            'services.github.client_secret' => 'github-secret',
            'services.github.redirect' => 'http://localhost/auth/github/callback',
        ]);

        Http::preventStrayRequests();
        Http::fake([
            'https://github.com/login/oauth/access_token' => Http::response([
                'access_token' => 'github-access-token',
                'token_type' => 'bearer',
            ]),
            'https://api.github.com/user' => Http::response([
                'id' => 13579,
                'name' => 'Octo Learner',
                'login' => 'octo-learner',
                'avatar_url' => 'https://avatars.githubusercontent.com/u/13579',
            ]),
            'https://api.github.com/user/emails' => Http::response([
                ['email' => 'octo@example.com', 'primary' => true, 'verified' => true],
                ['email' => 'unverified@example.com', 'primary' => false, 'verified' => false],
            ]),
        ]);

        $response = $this->withSession([
            'oauth_state_github' => 'github-state',
            'oauth_verifier_github' => 'github-verifier',
        ])->get('/auth/github/callback?code=authorization-code&state=github-state');

        $response->assertRedirect('/');
        $this->assertDatabaseHas('users', [
            'email' => 'octo@example.com',
            'provider' => 'github',
            'provider_id' => '13579',
        ]);
        $this->assertAuthenticated();
    }

    public function test_google_callback_creates_a_user_with_a_verified_email(): void
    {
        config([
            'services.google.client_id' => 'google-client',
            'services.google.client_secret' => 'google-secret',
            'services.google.redirect' => 'http://localhost/auth/google/callback',
        ]);

        Http::preventStrayRequests();
        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'google-access-token',
                'token_type' => 'Bearer',
            ]),
            'https://openidconnect.googleapis.com/v1/userinfo' => Http::response([
                'sub' => 'google-user-2468',
                'name' => 'Google Learner',
                'email' => 'google@example.com',
                'email_verified' => true,
                'picture' => 'https://example.com/avatar.png',
            ]),
        ]);

        $response = $this->withSession([
            'oauth_state_google' => 'google-state',
            'oauth_verifier_google' => 'google-verifier',
        ])->get('/auth/google/callback?code=authorization-code&state=google-state');

        $response->assertRedirect('/');
        $this->assertDatabaseHas('users', [
            'email' => 'google@example.com',
            'provider' => 'google',
            'provider_id' => 'google-user-2468',
        ]);
        $this->assertAuthenticated();
    }

    public function test_social_callback_rejects_a_mismatched_state(): void
    {
        $response = $this->withSession([
            'oauth_state_google' => 'expected-state',
            'oauth_verifier_google' => 'google-verifier',
        ])->get('/auth/google/callback?code=authorization-code&state=wrong-state');

        $response->assertForbidden();
        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_social_callback_does_not_link_an_existing_email_automatically(): void
    {
        $existingUser = User::factory()->create([
            'email' => 'google@example.com',
        ]);
        config([
            'services.google.client_id' => 'google-client',
            'services.google.client_secret' => 'google-secret',
            'services.google.redirect' => 'http://localhost/auth/google/callback',
        ]);

        Http::preventStrayRequests();
        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'google-access-token',
            ]),
            'https://openidconnect.googleapis.com/v1/userinfo' => Http::response([
                'sub' => 'google-user-2468',
                'name' => 'Google Learner',
                'email' => 'google@example.com',
                'email_verified' => true,
            ]),
        ]);

        $response = $this->withSession([
            'oauth_state_google' => 'google-state',
            'oauth_verifier_google' => 'google-verifier',
        ])->get('/auth/google/callback?code=authorization-code&state=google-state');

        $response->assertRedirect('/login')
            ->assertSessionHasErrors(['oauth']);
        $this->assertGuest();
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'id' => $existingUser->id,
            'provider' => null,
            'provider_id' => null,
        ]);
    }

    public function test_google_callback_does_not_create_an_unverified_account(): void
    {
        config([
            'services.google.client_id' => 'google-client',
            'services.google.client_secret' => 'google-secret',
            'services.google.redirect' => 'http://localhost/auth/google/callback',
        ]);

        Http::preventStrayRequests();
        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'google-access-token',
            ]),
            'https://openidconnect.googleapis.com/v1/userinfo' => Http::response([
                'sub' => 'google-user-2468',
                'name' => 'Google Learner',
                'email' => 'google@example.com',
                'email_verified' => false,
            ]),
        ]);

        $response = $this->withSession([
            'oauth_state_google' => 'google-state',
            'oauth_verifier_google' => 'google-verifier',
        ])->get('/auth/google/callback?code=authorization-code&state=google-state');

        $response->assertRedirect('/login')
            ->assertSessionHasErrors(['oauth']);
        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }
}
