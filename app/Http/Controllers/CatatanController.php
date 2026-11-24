<?php

namespace App\Http\Controllers;

use App\Models\Catatan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;

class CatatanController extends Controller
{
    public function index(Request $request): View
    {
        return $this->listing($request, false);
    }

    public function arsip(Request $request): View
    {
        return $this->listing($request, true);
    }

    public function create(): View
    {
        return view('Catatan.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->noteRules(), $this->noteMessages());

        $data = [
            'judul' => $validated['judul'],
            'isi' => $validated['isi'],
        ];

        if ($request->hasFile('lampiran')) {
            $data['lampiran'] = $this->storeAttachment($request->file('lampiran'));
        }

        $request->user()->catatan()->create($data);

        return redirect()->route('catatan.index')->with('sukses', 'Catatan berhasil disimpan!');
    }

    public function show(Request $request, int $id): View
    {
        $catatan = $this->findOwnedNote($request, $id);
        $attachmentUrl = $catatan->lampiran
            ? route('catatan.attachment', $catatan)
            : null;

        return view('Catatan.show', compact('catatan', 'attachmentUrl'));
    }

    public function edit(Request $request, int $id): View
    {
        $catatan = $this->findOwnedNote($request, $id);
        $attachmentUrl = $catatan->lampiran
            ? route('catatan.attachment', $catatan)
            : null;

        return view('Catatan.edit', compact('catatan', 'attachmentUrl'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $catatan = $this->findOwnedNote($request, $id);
        $validated = $request->validate($this->noteRules(), $this->noteMessages());

        $data = [
            'judul' => $validated['judul'],
            'isi' => $validated['isi'],
        ];
        $oldAttachment = $catatan->lampiran;
        $newAttachment = null;

        if ($request->hasFile('lampiran')) {
            $newAttachment = $this->storeAttachment($request->file('lampiran'));
            $data['lampiran'] = $newAttachment;
        }

        $catatan->update($data);

        if ($newAttachment !== null && $oldAttachment !== null) {
            $this->deleteAttachment($oldAttachment);
        }

        return redirect()->route('catatan.show', $catatan)->with('sukses', 'Catatan berhasil diperbarui!');
    }

    public function confirmDelete(Request $request, int $id): View
    {
        $catatan = $this->findOwnedNote($request, $id);

        return view('Catatan.delete', compact('catatan'));
    }

    public function downloadAttachment(Request $request, int $id)
    {
        $catatan = $this->findOwnedNote($request, $id);

        abort_unless(
            $catatan->lampiran !== null && Storage::disk('local')->exists($catatan->lampiran),
            404,
        );

        return Storage::disk('local')->download($catatan->lampiran);
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $catatan = $this->findOwnedNote($request, $id);
        $attachment = $catatan->lampiran;
        $catatan->delete();

        if ($attachment !== null) {
            $this->deleteAttachment($attachment);
        }

        return redirect()->route('catatan.index')->with('sukses', 'Catatan berhasil dihapus!');
    }

    public function archive(Request $request, int $id): RedirectResponse
    {
        $catatan = $this->findOwnedNote($request, $id);
        $catatan->is_archived = true;
        $catatan->save();

        return redirect()->route('catatan.index')->with('sukses', 'Catatan telah diarsipkan.');
    }

    public function unarchive(Request $request, int $id): RedirectResponse
    {
        $catatan = $this->findOwnedNote($request, $id);
        $catatan->is_archived = false;
        $catatan->save();

        return redirect()->route('catatan.arsip')->with('sukses', 'Catatan dipulihkan dari arsip.');
    }

    public function export(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $catatan = $request->user()->catatan()
            ->where('is_archived', false)
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        return response()->streamDownload(function () use ($catatan): void {
            $output = fopen('php://output', 'wb');

            if ($output === false) {
                throw new RuntimeException('Tidak dapat membuka output CSV.');
            }

            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, ['Judul', 'Isi', 'Tanggal Dibuat'], ',', '"', '');

            foreach ($catatan->cursor() as $item) {
                fputcsv($output, [
                    $this->spreadsheetSafeValue($item->judul),
                    $this->spreadsheetSafeValue($item->isi),
                    $item->created_at->toDateTimeString(),
                ], ',', '"', '');
            }

            fclose($output);
        }, 'catatan_aktif_'.now()->format('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function importForm(): View
    {
        return view('Catatan.import');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'extensions:csv,txt', 'max:2048'],
        ]);

        $handle = fopen($request->file('file')->getRealPath(), 'rb');

        if ($handle === false) {
            throw new RuntimeException('Tidak dapat membaca file CSV yang diunggah.');
        }

        try {
            $header = fgetcsv($handle, null, ',', '"', '');

            if ($header === false) {
                throw ValidationException::withMessages([
                    'file' => 'File CSV tidak boleh kosong.',
                ]);
            }

            $firstColumn = strtolower(trim(ltrim((string) ($header[0] ?? ''), "\xEF\xBB\xBF")));
            $secondColumn = strtolower(trim((string) ($header[1] ?? '')));

            if ($firstColumn !== 'judul' || $secondColumn !== 'isi') {
                throw ValidationException::withMessages([
                    'file' => 'Header CSV harus dimulai dengan kolom Judul dan Isi.',
                ]);
            }

            $rows = [];
            $errors = [];
            $rowNumber = 1;

            while (($row = fgetcsv($handle, null, ',', '"', '')) !== false) {
                if (count($row) === 1 && trim((string) $row[0]) === '') {
                    continue;
                }

                $rowNumber++;

                if ($rowNumber > 5001) {
                    $errors[] = 'File CSV tidak boleh berisi lebih dari 5000 catatan.';

                    break;
                }

                $judul = trim((string) ($row[0] ?? ''));
                $isi = trim((string) ($row[1] ?? ''));

                if ($judul === '' || $isi === '') {
                    $errors[] = "Baris {$rowNumber}: judul dan isi wajib diisi.";

                    continue;
                }

                if (mb_strlen($judul) > 255) {
                    $errors[] = "Baris {$rowNumber}: judul maksimal 255 karakter.";

                    continue;
                }

                $rows[] = compact('judul', 'isi');
            }
        } finally {
            fclose($handle);
        }

        if ($errors !== []) {
            throw ValidationException::withMessages(['file' => $errors]);
        }

        if ($rows === []) {
            throw ValidationException::withMessages([
                'file' => 'File CSV tidak berisi catatan untuk diimpor.',
            ]);
        }

        DB::transaction(function () use ($request, $rows): void {
            foreach ($rows as $row) {
                $request->user()->catatan()->create($row);
            }
        });

        return redirect()->route('catatan.index')
            ->with('sukses', 'Berhasil mengimpor '.count($rows).' catatan.');
    }

    private function listing(Request $request, bool $archived): View
    {
        $filters = $request->validate([
            'cari' => ['nullable', 'string', 'max:100'],
            'urut' => ['nullable', Rule::in(['terbaru', 'terlama'])],
        ]);
        $direction = ($filters['urut'] ?? 'terbaru') === 'terlama' ? 'asc' : 'desc';
        $query = $request->user()->catatan()->where('is_archived', $archived);

        $this->applySearch($query, $filters['cari'] ?? null);

        $catatan = $query
            ->orderBy('created_at', $direction)
            ->orderBy('id', $direction)
            ->paginate(5)
            ->withQueryString();
        $jumlahAktif = $request->user()->catatan()->where('is_archived', false)->count();
        $jumlahArsip = $request->user()->catatan()->where('is_archived', true)->count();

        return view($archived ? 'Catatan.arsip' : 'Catatan.index', compact(
            'catatan',
            'jumlahAktif',
            'jumlahArsip',
            'filters',
            'archived',
        ));
    }

    private function applySearch(HasMany $query, ?string $search): void
    {
        if ($search === null || $search === '') {
            return;
        }

        $keyword = "%{$search}%";
        $query->where(function (Builder $query) use ($keyword): void {
            $query->where('judul', 'like', $keyword)
                ->orWhere('isi', 'like', $keyword);
        });
    }

    private function findOwnedNote(Request $request, int $id): Catatan
    {
        return $request->user()->catatan()->findOrFail($id);
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function noteRules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:255'],
            'isi' => ['required', 'string'],
            'lampiran' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf,txt',
                'extensions:jpg,jpeg,png,pdf,txt',
                'max:2048',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function noteMessages(): array
    {
        return [
            'judul.required' => 'Judul wajib diisi.',
            'judul.max' => 'Judul maksimal 255 karakter.',
            'isi.required' => 'Isi catatan tidak boleh kosong.',
            'lampiran.mimes' => 'Lampiran harus berupa JPG, JPEG, PNG, PDF, atau TXT.',
            'lampiran.extensions' => 'Ekstensi lampiran harus JPG, JPEG, PNG, PDF, atau TXT.',
            'lampiran.max' => 'Ukuran lampiran maksimal 2 MB.',
        ];
    }

    private function storeAttachment(UploadedFile $file): string
    {
        $path = $file->store('lampiran', 'local');

        if (! is_string($path)) {
            throw new RuntimeException('Lampiran gagal disimpan.');
        }

        return $path;
    }

    private function deleteAttachment(string $path): void
    {
        $disk = Storage::disk('local');

        if ($disk->exists($path) && ! $disk->delete($path)) {
            throw new RuntimeException('Lampiran gagal dihapus dari penyimpanan.');
        }
    }

    private function spreadsheetSafeValue(string $value): string
    {
        return preg_match('/^[\x00-\x20]*[=+\-@]/', $value) === 1
            ? "'".$value
            : $value;
    }
}
