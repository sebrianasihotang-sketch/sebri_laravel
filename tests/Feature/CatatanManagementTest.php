<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CatatanManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_valid_private_note_with_an_attachment(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/catatan', [
            'judul' => 'Catatan pertama',
            'isi' => 'Isi catatan pertama',
            'lampiran' => UploadedFile::fake()->image('catatan.png'),
        ]);

        $response->assertRedirect(route('catatan.index'));
        $catatan = $user->catatan()->firstOrFail();
        $this->assertDatabaseHas('catatan', [
            'id' => $catatan->id,
            'user_id' => $user->id,
            'judul' => 'Catatan pertama',
            'is_archived' => false,
        ]);
        Storage::disk('local')->assertExists($catatan->lampiran);

        $this->get(route('catatan.attachment', $catatan))->assertDownload();
    }

    public function test_note_validation_rejects_empty_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('catatan.create'))
            ->post('/catatan', ['judul' => '', 'isi' => '']);

        $response->assertRedirect(route('catatan.create'))
            ->assertSessionHasErrors(['judul', 'isi']);
        $this->assertDatabaseCount('catatan', 0);
    }

    public function test_note_validation_rejects_titles_longer_than_255_characters(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('catatan.create'))
            ->post('/catatan', [
                'judul' => str_repeat('a', 256),
                'isi' => 'Isi catatan',
            ]);

        $response->assertRedirect(route('catatan.create'))
            ->assertSessionHasErrors(['judul']);
        $this->assertDatabaseCount('catatan', 0);
    }

    public function test_note_upload_rejects_an_executable_file(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('catatan.create'))
            ->post('/catatan', [
                'judul' => 'Catatan',
                'isi' => 'Isi catatan',
                'lampiran' => UploadedFile::fake()->create('script.exe', 5, 'application/x-msdownload'),
            ]);

        $response->assertRedirect(route('catatan.create'))
            ->assertSessionHasErrors(['lampiran']);
        $this->assertDatabaseCount('catatan', 0);
        $this->assertSame([], Storage::disk('local')->allFiles('lampiran'));
    }

    public function test_user_can_replace_an_attachment_and_the_old_file_is_removed(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        Storage::disk('local')->put('lampiran/lama.txt', 'Lampiran lama');
        $catatan = $user->catatan()->create([
            'judul' => 'Catatan',
            'isi' => 'Isi',
            'lampiran' => 'lampiran/lama.txt',
        ]);

        $response = $this->actingAs($user)->put("/catatan/{$catatan->id}", [
            'judul' => 'Catatan',
            'isi' => 'Isi diperbarui',
            'lampiran' => UploadedFile::fake()->createWithContent('baru.txt', 'Lampiran baru'),
        ]);

        $response->assertRedirect(route('catatan.show', $catatan));
        $catatan->refresh();
        Storage::disk('local')->assertMissing('lampiran/lama.txt');
        Storage::disk('local')->assertExists($catatan->lampiran);
        $this->assertSame('Isi diperbarui', $catatan->isi);
    }

    public function test_user_can_archive_and_restore_their_note(): void
    {
        $user = User::factory()->create();
        $catatan = $user->catatan()->create([
            'judul' => 'Catatan arsip',
            'isi' => 'Isi catatan',
        ]);

        $this->actingAs($user)
            ->post(route('catatan.archive', $catatan))
            ->assertRedirect(route('catatan.index'));

        $this->assertDatabaseHas('catatan', [
            'id' => $catatan->id,
            'is_archived' => true,
        ]);
        $this->get(route('catatan.index'))->assertDontSee('Catatan arsip');
        $this->get(route('catatan.arsip'))->assertSee('Catatan arsip');

        $this->post(route('catatan.unarchive', $catatan))
            ->assertRedirect(route('catatan.arsip'));

        $this->assertDatabaseHas('catatan', [
            'id' => $catatan->id,
            'is_archived' => false,
        ]);
    }
}
