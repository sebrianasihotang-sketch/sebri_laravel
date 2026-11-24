<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CatatanAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_before_accessing_notes(): void
    {
        $response = $this->get('/catatan');

        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_create_a_note(): void
    {
        $response = $this->post('/catatan', [
            'judul' => 'Catatan rahasia',
            'isi' => 'Isi catatan',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('catatan', 0);
    }

    #[DataProvider('foreignNoteActions')]
    public function test_note_actions_return_not_found_for_another_users_note(string $method, string $uri): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $catatan = $owner->catatan()->create([
            'judul' => 'Catatan milik pemilik',
            'isi' => 'Isi privat',
        ]);

        $response = $this->actingAs($otherUser)->call($method, sprintf($uri, $catatan->id), [
            'judul' => 'Perubahan tidak diizinkan',
            'isi' => 'Isi baru',
        ]);

        $response->assertNotFound();
        $this->assertDatabaseHas('catatan', [
            'id' => $catatan->id,
            'judul' => 'Catatan milik pemilik',
            'user_id' => $owner->id,
        ]);
    }

    public static function foreignNoteActions(): array
    {
        return [
            'detail' => ['GET', '/catatan/%d'],
            'edit form' => ['GET', '/catatan/%d/edit'],
            'delete confirmation' => ['GET', '/catatan/%d/hapus'],
            'attachment download' => ['GET', '/catatan/%d/lampiran'],
            'update' => ['PUT', '/catatan/%d'],
            'delete' => ['DELETE', '/catatan/%d'],
            'archive' => ['POST', '/catatan/%d/archive'],
            'restore' => ['POST', '/catatan/%d/unarchive'],
        ];
    }
}
