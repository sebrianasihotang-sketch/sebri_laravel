<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_displayed_to_guests(): void
    {
        $response = $this->get('/login');

        $response->assertOk()
            ->assertSee('Masuk ke akun')
            ->assertDontSee('Lanjutkan dengan Google')
            ->assertDontSee('Lanjutkan dengan GitHub');
    }

    public function test_user_is_authenticated_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'astronaut@example.com',
            'password' => 'password',
        ]);

        $response = $this->post('/login', [
            'email' => 'astronaut@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/catatan');
        $this->assertAuthenticatedAs($user);
    }

    public function test_missing_credentials_return_validation_errors(): void
    {
        $response = $this->from('/login')->post('/login');

        $response->assertRedirect('/login')
            ->assertSessionHasErrors(['email', 'password']);
        $this->assertGuest();
    }

    public function test_invalid_credentials_do_not_authenticate_the_user(): void
    {
        User::factory()->create([
            'email' => 'astronaut@example.com',
            'password' => 'password',
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'astronaut@example.com',
            'password' => 'incorrect-password',
        ]);

        $response->assertRedirect('/login')
            ->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_authenticated_user_can_log_out(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }
}
