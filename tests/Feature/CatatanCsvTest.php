<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CatatanCsvTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_downloads_only_the_users_active_notes_and_protects_spreadsheet_formulas(): void
    {
        $this->travelTo('2026-11-24 12:00:00');
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $user->catatan()->create([
            'judul' => '=HYPERLINK("https://example.test")',
            'isi' => 'Catatan aktif',
        ]);
        $archived = $user->catatan()->create([
            'judul' => 'Catatan arsip',
            'isi' => 'Tidak diekspor',
        ]);
        $archived->is_archived = true;
        $archived->save();
        $otherUser->catatan()->create([
            'judul' => 'Catatan pengguna lain',
            'isi' => 'Tidak diekspor',
        ]);

        $response = $this->actingAs($user)->get(route('catatan.export'));

        $response->assertDownload('catatan_aktif_2026-11-24.csv');
        $content = $response->streamedContent();
        $this->assertStringContainsString("\xEF\xBB\xBFJudul,Isi,", $content);
        $this->assertStringContainsString('"Tanggal Dibuat"', $content);
        $this->assertStringContainsString("'=HYPERLINK", $content);
        $this->assertStringNotContainsString('Catatan arsip', $content);
        $this->assertStringNotContainsString('Catatan pengguna lain', $content);
    }

    public function test_import_creates_all_rows_for_the_authenticated_user(): void
    {
        $user = User::factory()->create();
        $file = UploadedFile::fake()->createWithContent(
            'catatan.csv',
            "Judul,Isi,Tanggal Dibuat\nIde proyek,\"Belajar Laravel\",2026-02-10 10:20:00\nBelanja,\"Susu, roti\",2026-02-10 10:30:00\n",
        );

        $response = $this->actingAs($user)->post(route('catatan.import.store'), [
            'file' => $file,
        ]);

        $response->assertRedirect(route('catatan.index'))
            ->assertSessionHas('sukses', 'Berhasil mengimpor 2 catatan.');
        $this->assertDatabaseHas('catatan', [
            'user_id' => $user->id,
            'judul' => 'Ide proyek',
            'isi' => 'Belajar Laravel',
            'is_archived' => false,
        ]);
        $this->assertDatabaseHas('catatan', [
            'user_id' => $user->id,
            'judul' => 'Belanja',
            'isi' => 'Susu, roti',
        ]);
        $this->assertDatabaseCount('catatan', 2);
    }

    public function test_import_rejects_invalid_rows_without_partially_creating_notes(): void
    {
        $user = User::factory()->create();
        $file = UploadedFile::fake()->createWithContent(
            'catatan.csv',
            "Judul,Isi\nCatatan valid,Isi valid\n,Isi tanpa judul\n",
        );

        $response = $this->actingAs($user)
            ->from(route('catatan.import'))
            ->post(route('catatan.import.store'), ['file' => $file]);

        $response->assertRedirect(route('catatan.import'))
            ->assertSessionHasErrors(['file']);
        $this->assertDatabaseCount('catatan', 0);
    }
}
