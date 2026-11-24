<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    public function redirectToProvider(Request $request, string $provider): RedirectResponse
    {
        if (! $this->hasCredentials($provider)) {
            return $this->loginError("Login dengan ".ucfirst($provider).' belum dikonfigurasi.');
        }

        $state = Str::random(64);
        $verifier = Str::random(64);
        $request->session()->put("oauth_state_{$provider}", $state);
        $request->session()->put("oauth_verifier_{$provider}", $verifier);

        $parameters = [
            'client_id' => config("services.{$provider}.client_id"),
            'redirect_uri' => config("services.{$provider}.redirect"),
            'response_type' => 'code',
            'state' => $state,
            'code_challenge' => $this->codeChallenge($verifier),
            'code_challenge_method' => 'S256',
        ];

        if ($provider === 'github') {
            $parameters['scope'] = 'read:user user:email';
            $parameters['allow_signup'] = 'true';
            $url = 'https://github.com/login/oauth/authorize';
        } else {
            $parameters['scope'] = 'openid email profile';
            $parameters['prompt'] = 'select_account';
            $url = 'https://accounts.google.com/o/oauth2/v2/auth';
        }

        return redirect()->away($url.'?'.http_build_query($parameters, '', '&', PHP_QUERY_RFC3986));
    }

    public function handleProviderCallback(Request $request, string $provider): RedirectResponse
    {
        $state = $request->session()->pull("oauth_state_{$provider}");
        $verifier = $request->session()->pull("oauth_verifier_{$provider}");
        $receivedState = $request->query('state');

        abort_unless(
            is_string($state)
                && is_string($receivedState)
                && hash_equals($state, $receivedState)
                && is_string($verifier),
            403,
        );

        if ($request->filled('error')) {
            return $this->loginError('Login dengan '.ucfirst($provider).' dibatalkan.');
        }

        $code = $request->query('code');

        abort_unless(is_string($code) && $code !== '', 400);

        if (! $this->hasCredentials($provider)) {
            return $this->loginError("Login dengan ".ucfirst($provider).' belum dikonfigurasi.');
        }

        $tokenResponse = Http::asForm()
            ->acceptJson()
            ->timeout(10)
            ->post($this->tokenUrl($provider), [
                'client_id' => config("services.{$provider}.client_id"),
                'client_secret' => config("services.{$provider}.client_secret"),
                'code' => $code,
                'redirect_uri' => config("services.{$provider}.redirect"),
                'code_verifier' => $verifier,
                'grant_type' => 'authorization_code',
            ]);
        $tokenResponse->throw();

        $accessToken = $tokenResponse->json('access_token');

        if (! is_string($accessToken) || $accessToken === '') {
            return $this->loginError('Penyedia tidak mengembalikan token akses yang valid.');
        }

        $profile = $provider === 'github'
            ? $this->githubProfile($accessToken)
            : $this->googleProfile($accessToken);

        if ($profile === null) {
            return $this->loginError('Akun penyedia harus memiliki email utama yang terverifikasi.');
        }

        $user = User::query()
            ->where('provider', $provider)
            ->where('provider_id', $profile['provider_id'])
            ->first();

        if (! $user) {
            if (User::query()->where('email', $profile['email'])->exists()) {
                return $this->loginError('Email ini sudah terdaftar. Masuk dengan kata sandi terlebih dahulu.');
            }

            $user = User::create([
                'name' => $profile['name'],
                'email' => $profile['email'],
                'email_verified_at' => now(),
                'password' => Str::random(64),
                'provider' => $provider,
                'provider_id' => $profile['provider_id'],
                'avatar_url' => $profile['avatar_url'],
            ]);

            event(new Registered($user));
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended('/');
    }

    private function hasCredentials(string $provider): bool
    {
        return filled(config("services.{$provider}.client_id"))
            && filled(config("services.{$provider}.client_secret"))
            && filled(config("services.{$provider}.redirect"));
    }

    private function tokenUrl(string $provider): string
    {
        return $provider === 'github'
            ? 'https://github.com/login/oauth/access_token'
            : 'https://oauth2.googleapis.com/token';
    }

    private function codeChallenge(string $verifier): string
    {
        return rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');
    }

    /**
     * @return array{provider_id: string, email: string, name: string, avatar_url: ?string}|null
     */
    private function githubProfile(string $accessToken): ?array
    {
        $github = Http::withToken($accessToken)
            ->withHeaders([
                'Accept' => 'application/vnd.github+json',
                'User-Agent' => config('app.name'),
                'X-GitHub-Api-Version' => '2022-11-28',
            ])
            ->timeout(10);

        $profile = $github->get('https://api.github.com/user')->throw()->json();
        $emails = $github->get('https://api.github.com/user/emails')->throw()->json();

        if (! is_array($profile) || ! is_array($emails)) {
            return null;
        }

        $primaryEmail = collect($emails)->first(
            fn ($email): bool => is_array($email)
                && ($email['primary'] ?? false) === true
                && ($email['verified'] ?? false) === true,
        );

        if (! is_array($primaryEmail)) {
            return null;
        }

        $name = $profile['name'] ?? $profile['login'] ?? '';

        return $this->normalizedProfile(
            $profile['id'] ?? null,
            $primaryEmail['email'] ?? null,
            is_string($name) ? $name : '',
            $profile['avatar_url'] ?? null,
        );
    }

    /**
     * @return array{provider_id: string, email: string, name: string, avatar_url: ?string}|null
     */
    private function googleProfile(string $accessToken): ?array
    {
        $profile = Http::withToken($accessToken)
            ->acceptJson()
            ->timeout(10)
            ->get('https://openidconnect.googleapis.com/v1/userinfo')
            ->throw()
            ->json();

        if (! is_array($profile) || ($profile['email_verified'] ?? false) !== true) {
            return null;
        }

        return $this->normalizedProfile(
            $profile['sub'] ?? null,
            $profile['email'] ?? null,
            $profile['name'] ?? '',
            $profile['picture'] ?? null,
        );
    }

    /**
     * @return array{provider_id: string, email: string, name: string, avatar_url: ?string}|null
     */
    private function normalizedProfile(
        mixed $providerId,
        mixed $email,
        mixed $name,
        mixed $avatarUrl,
    ): ?array {
        if ((! is_string($providerId) && ! is_int($providerId))
            || ! is_string($email)
            || filter_var($email, FILTER_VALIDATE_EMAIL) === false
        ) {
            return null;
        }

        $email = Str::lower($email);
        $name = is_string($name) ? trim($name) : '';
        $name = $name !== '' ? $name : Str::before($email, '@');

        return [
            'provider_id' => (string) $providerId,
            'email' => $email,
            'name' => mb_substr($name, 0, 255),
            'avatar_url' => is_string($avatarUrl) && filter_var($avatarUrl, FILTER_VALIDATE_URL)
                ? $avatarUrl
                : null,
        ];
    }

    private function loginError(string $message): RedirectResponse
    {
        return redirect()->route('login')->withErrors(['oauth' => $message]);
    }
}
