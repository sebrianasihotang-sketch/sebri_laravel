<?php

namespace Tests\Feature;

use App\Models\Catatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatatanDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_delete_confirmation_page_is_shown(): void
    {
        $user = User::factory()->create();
        $catatan = $user->catatan()->create([
            'judul' => 'Catatan untuk dihapus',
            'isi' => 'Isi catatan',
        ]);

        $response = $this->actingAs($user)->get("/catatan/{$catatan->id}/hapus");

        $response->assertOk()
            ->assertSee('Konfirmasi Hapus Catatan')
            ->assertSee($catatan->judul);
    }

    public function test_catatan_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $catatan = $user->catatan()->create([
            'judul' => 'Catatan untuk dihapus',
            'isi' => 'Isi catatan',
        ]);

        $response = $this->actingAs($user)->delete("/catatan/{$catatan->id}");

        $response->assertRedirect(route('catatan.index'))
            ->assertSessionHas('sukses', 'Catatan berhasil dihapus!');
        $this->assertDatabaseMissing('catatan', ['id' => $catatan->id]);
    }
}
