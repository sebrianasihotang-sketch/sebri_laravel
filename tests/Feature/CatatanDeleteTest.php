<?php

namespace Tests\Feature;

use App\Models\Catatan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatatanDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_catatan_can_be_deleted(): void
    {
        $catatan = Catatan::create([
            'judul' => 'Catatan untuk dihapus',
            'isi' => 'Isi catatan',
        ]);

        $response = $this->delete("/catatan/{$catatan->id}");

        $response->assertRedirect('/catatan')
            ->assertSessionHas('sukses', 'Catatan berhasil dihapus!');
        $this->assertDatabaseMissing('catatan', ['id' => $catatan->id]);
    }
}
