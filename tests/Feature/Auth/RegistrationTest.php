<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_page_offers_email_registration_without_social_login(): void
    {
        $response = $this->get('/register');

        $response->assertOk()
            ->assertSee('Buat akun')
            ->assertDontSee('Lanjutkan dengan Google')
            ->assertDontSee('Lanjutkan dengan GitHub');
    }

    public function test_registration_stores_the_user_and_authenticates_them(): void
    {
        $response = $this->post('/register', [
            'name' => 'Ruang Belajar',
            'email' => 'ruang@example.com',
            'password' => 'StrongPassword123!',
            'password_confirmation' => 'StrongPassword123!',
        ]);

        $response->assertRedirect('/catatan');
        $this->assertDatabaseHas('users', [
            'name' => 'Ruang Belajar',
            'email' => 'ruang@example.com',
        ]);
        $this->assertAuthenticated();
    }

    public function test_registration_rejects_an_email_that_is_already_in_use(): void
    {
        User::factory()->create(['email' => 'ruang@example.com']);

        $response = $this->from('/register')->post('/register', [
            'name' => 'Ruang Belajar Baru',
            'email' => 'ruang@example.com',
            'password' => 'StrongPassword123!',
            'password_confirmation' => 'StrongPassword123!',
        ]);

        $response->assertRedirect('/register')
            ->assertSessionHasErrors(['email']);
        $this->assertGuest();
        $this->assertDatabaseCount('users', 1);
    }
}
