<?php

namespace App\Services;

use App\Models\Event;
use App\Models\GuestGroup;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use RuntimeException;
use SimpleXMLElement;
use ZipArchive;

class GuestImportService
{
    private const SESSION_PREFIX = 'guest_import_preview.';

    private const SUPPORTED_HEADERS = [
        'name',
        'phone',
        'group_name',
        'address_note',
        'max_pax',
        'status',
        'is_vip',
        'needs_physical_invitation',
    ];

    public function buildPreview(Event $event, UploadedFile $file): array
    {
        $rows = match (Str::lower($file->getClientOriginalExtension())) {
            'xlsx' => $this->parseXlsx($file),
            default => $this->parseCsv($file),
        };

        if ($rows === []) {
            throw new RuntimeException('File impor tidak berisi data yang bisa dibaca.');
        }

        $headers = array_map(
            fn ($header) => Str::snake(str_replace("\xEF\xBB\xBF", '', trim((string) $header))),
            array_shift($rows) ?: []
        );

        if ($headers === []) {
            throw new RuntimeException('File impor wajib memiliki header di baris pertama.');
        }

        $previewRows = [];
        $validCount = 0;
        $invalidCount = 0;
        $groupNames = [];

        foreach ($rows as $index => $row) {
            $normalizedRow = $this->normalizeColumns($headers, $row);

            if ($this->rowIsEmpty($normalizedRow)) {
                continue;
            }

            $mapped = $this->mapRow($normalizedRow);
            $errors = $this->validateMappedRow($mapped);

            if ($mapped['group_name']) {
                $groupNames[] = $mapped['group_name'];
            }

            if ($errors === []) {
                $validCount++;
            } else {
                $invalidCount++;
            }

            $previewRows[] = [
                'row_number' => $index + 2,
                'data' => $mapped,
                'errors' => $errors,
            ];
        }

        if ($previewRows === []) {
            throw new RuntimeException('Tidak ada baris tamu yang terdeteksi dari file impor.');
        }

        $preview = [
            'file_name' => $file->getClientOriginalName(),
            'headers' => array_values(array_intersect($headers, self::SUPPORTED_HEADERS)),
            'rows' => $previewRows,
            'summary' => [
                'total_rows' => count($previewRows),
                'valid_rows' => $validCount,
                'invalid_rows' => $invalidCount,
                'detected_groups' => array_values(array_unique(array_filter($groupNames))),
            ],
        ];

        Session::put($this->sessionKey($event), $preview);

        return $preview;
    }

    public function getPreview(Event $event): ?array
    {
        return Session::get($this->sessionKey($event));
    }

    public function clearPreview(Event $event): void
    {
        Session::forget($this->sessionKey($event));
    }

    public function commitPreview(Event $event): array
    {
        $preview = $this->getPreview($event);

        if (! $preview) {
            throw new RuntimeException('Preview impor tidak ditemukan. Unggah file lagi sebelum mengimpor.');
        }

        return $preview;
    }

    public function resolveGroupMap(Event $event, array $groupNames): array
    {
        $existing = $event->guestGroups()
            ->whereIn('name', $groupNames)
            ->get()
            ->keyBy('name');

        $groupMap = [];

        foreach ($groupNames as $groupName) {
            if (! $groupName) {
                continue;
            }

            $group = $existing[$groupName] ?? GuestGroup::create([
                'event_id' => $event->id,
                'name' => $groupName,
                'slug' => $this->uniqueSlug($event, $groupName),
            ]);

            $groupMap[$groupName] = $group;
        }

        return $groupMap;
    }

    private function parseCsv(UploadedFile $file): array
    {
        return array_map('str_getcsv', file($file->getRealPath(), FILE_IGNORE_NEW_LINES) ?: []);
    }

    private function parseXlsx(UploadedFile $file): array
    {
        $zip = new ZipArchive();

        if ($zip->open($file->getRealPath()) !== true) {
            throw new RuntimeException('File XLSX tidak bisa dibuka.');
        }

        $sharedStrings = $this->readSharedStrings($zip);
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');

        if (! $sheetXml) {
            $zip->close();
            throw new RuntimeException('Worksheet pertama pada XLSX tidak ditemukan.');
        }

        $xml = new SimpleXMLElement($sheetXml);
        $xml->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

        $rows = [];

        foreach ($xml->xpath('//x:sheetData/x:row') ?: [] as $row) {
            $values = [];

            foreach ($row->xpath('x:c') ?: [] as $cell) {
                $reference = (string) $cell['r'];
                $columnIndex = $this->columnIndexFromReference($reference);
                $type = (string) $cell['t'];
                $value = '';

                if ($type === 's') {
                    $sharedIndex = (int) ($cell->v ?? 0);
                    $value = $sharedStrings[$sharedIndex] ?? '';
                } elseif ($type === 'inlineStr') {
                    $value = (string) ($cell->is->t ?? '');
                } else {
                    $value = (string) ($cell->v ?? '');
                }

                $values[$columnIndex] = $value;
            }

            if ($values === []) {
                continue;
            }

            ksort($values);
            $maxIndex = max(array_keys($values));
            $rowValues = [];

            for ($index = 0; $index <= $maxIndex; $index++) {
                $rowValues[] = $values[$index] ?? '';
            }

            $rows[] = $rowValues;
        }

        $zip->close();

        return $rows;
    }

    private function readSharedStrings(ZipArchive $zip): array
    {
        $xmlContent = $zip->getFromName('xl/sharedStrings.xml');

        if (! $xmlContent) {
            return [];
        }

        $xml = new SimpleXMLElement($xmlContent);
        $xml->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

        return array_map(
            fn ($item) => trim((string) collect($item->xpath('.//x:t') ?: [])->map(fn ($node) => (string) $node)->implode('')),
            $xml->xpath('//x:si') ?: []
        );
    }

    private function columnIndexFromReference(string $reference): int
    {
        $letters = preg_replace('/[^A-Z]/', '', strtoupper($reference));
        $index = 0;

        foreach (str_split($letters) as $letter) {
            $index = ($index * 26) + (ord($letter) - 64);
        }

        return max(1, $index) - 1;
    }

    private function normalizeColumns(array $headers, array $row): array
    {
        $data = [];

        foreach ($headers as $index => $header) {
            if (! in_array($header, self::SUPPORTED_HEADERS, true)) {
                continue;
            }

            $data[$header] = trim((string) Arr::get($row, $index, ''));
        }

        return $data;
    }

    private function rowIsEmpty(array $row): bool
    {
        return collect($row)->filter(fn ($value) => $value !== '')->isEmpty();
    }

    private function mapRow(array $row): array
    {
        return [
            'name' => $row['name'] ?? '',
            'phone' => $row['phone'] ?? '',
            'group_name' => $row['group_name'] ?? '',
            'address_note' => $row['address_note'] ?? '',
            'max_pax' => max(1, (int) ($row['max_pax'] ?: 1)),
            'status' => in_array(($row['status'] ?? 'active'), ['active', 'inactive'], true) ? ($row['status'] ?? 'active') : 'active',
            'is_vip' => $this->toBoolean($row['is_vip'] ?? ''),
            'needs_physical_invitation' => $this->toBoolean($row['needs_physical_invitation'] ?? ''),
        ];
    }

    private function validateMappedRow(array $row): array
    {
        $errors = [];

        if ($row['name'] === '') {
            $errors[] = 'Nama tamu wajib diisi.';
        }

        if ($row['phone'] !== '' && mb_strlen($row['phone']) > 30) {
            $errors[] = 'Nomor WhatsApp terlalu panjang.';
        }

        if ($row['max_pax'] < 1 || $row['max_pax'] > 10) {
            $errors[] = 'Max pax harus berada antara 1 sampai 10.';
        }

        return $errors;
    }

    private function toBoolean(string $value): bool
    {
        return in_array(Str::lower(trim($value)), ['1', 'true', 'yes', 'y', 'vip', 'ya'], true);
    }

    private function uniqueSlug(Event $event, string $name): string
    {
        $baseSlug = Str::slug($name) ?: 'group';
        $slug = $baseSlug;
        $index = 2;

        while ($event->guestGroups()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$index;
            $index++;
        }

        return $slug;
    }

    private function sessionKey(Event $event): string
    {
        return self::SESSION_PREFIX.$event->id;
    }
}
