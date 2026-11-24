<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatatanListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_filters_by_title_or_body_and_keeps_notes_private(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $user->catatan()->create([
            'judul' => 'Catatan biasa',
            'isi' => 'kata-kunci ada di isi',
        ]);
        $archived = $user->catatan()->create([
            'judul' => 'Arsip rahasia',
            'isi' => 'kata-kunci',
        ]);
        $archived->is_archived = true;
        $archived->save();
        $otherUser->catatan()->create([
            'judul' => 'Catatan pengguna lain',
            'isi' => 'kata-kunci',
        ]);

        $response = $this->actingAs($user)->get('/catatan?cari=kata-kunci');

        $response->assertOk()
            ->assertSee('Catatan biasa')
            ->assertDontSee('Arsip rahasia')
            ->assertDontSee('Catatan pengguna lain')
            ->assertSee('Anda memiliki 1 catatan aktif.')
            ->assertSee('Arsip (1)');

        $archiveResponse = $this->get('/catatan/arsip?cari=kata-kunci');

        $archiveResponse->assertOk()
            ->assertSee('Arsip rahasia')
            ->assertDontSee('Catatan biasa')
            ->assertSee('Anda memiliki 1 catatan di arsip.');
    }

    public function test_sorting_and_pagination_preserve_search_parameters(): void
    {
        $user = User::factory()->create();

        for ($index = 0; $index < 7; $index++) {
            $catatan = $user->catatan()->create([
                'judul' => "Keyword {$index}",
                'isi' => 'Isi pencarian',
            ]);
            $catatan->created_at = now()->subDays($index);
            $catatan->save();
        }

        $response = $this->actingAs($user)->get(route('catatan.index', [
            'cari' => 'keyword',
            'urut' => 'terlama',
        ]));

        $response->assertOk()
            ->assertSeeInOrder(['Keyword 6', 'Keyword 5', 'Keyword 4', 'Keyword 3', 'Keyword 2'])
            ->assertDontSee('Keyword 1')
            ->assertSee('page=2');

        $secondPage = $this->get(route('catatan.index', [
            'cari' => 'keyword',
            'urut' => 'terlama',
            'page' => 2,
        ]));

        $secondPage->assertOk()
            ->assertSeeInOrder(['Keyword 1', 'Keyword 0'])
            ->assertSee('value="keyword"', false)
            ->assertSee('<option value="terlama" selected>', false);
    }

    public function test_delete_modal_is_available_and_escapes_the_note_title(): void
    {
        $user = User::factory()->create();
        $user->catatan()->create([
            'judul' => '<script>alert("x")</script>',
            'isi' => 'Isi catatan',
        ]);

        $response = $this->actingAs($user)->get('/catatan');

        $response->assertOk()
            ->assertSee('id="deleteNoteModal"', false)
            ->assertSee('data-bs-toggle="modal"', false)
            ->assertSee('&lt;script&gt;', false)
            ->assertDontSee('<script>alert("x")</script>', false);
    }
}
