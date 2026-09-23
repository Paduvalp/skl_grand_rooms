<?php

namespace App\Support;

/**
 * Reads a guest list out of a CSV or Excel file.
 *
 * Excel files are read with PHP's own zip and XML support, so there is no
 * library to install. An .xlsx file is really a zip of XML sheets, and the
 * two parts we need are the shared text and the first worksheet.
 *
 * The file may have a header row or not, and the columns may be in either
 * order. Whatever looks like an Indian mobile number is taken as the phone,
 * and the other column, if there is one, is taken as the name.
 */
class ContactImport
{
    /** Words that mark a header row, so it is not imported as a guest. */
    private const HEADINGS = ['name', 'phone', 'mobile', 'number', 'contact', 'guest'];

    /**
     * @return array<int, array{name: string|null, phone: string}>  Rows worth importing.
     */
    public static function rows(string $path, string $extension): array
    {
        $raw = match (strtolower($extension)) {
            'xlsx' => self::fromXlsx($path),
            default => self::fromCsv($path),
        };

        $rows = [];

        foreach ($raw as $cells) {
            $cells = array_values(array_filter(array_map('trim', $cells), fn ($c) => $c !== ''));

            if (! $cells || self::isHeading($cells)) {
                continue;
            }

            $phone = null;
            $name = null;

            foreach ($cells as $cell) {
                if ($phone === null && ($found = \App\Models\SmsContact::normalise($cell))) {
                    $phone = $found;

                    continue;
                }

                // The first cell that is not the number, and has a letter in
                // it, is the guest's name.
                if ($name === null && preg_match('/\p{L}/u', $cell)) {
                    $name = mb_substr($cell, 0, 120);
                }
            }

            if ($phone) {
                $rows[] = ['name' => $name, 'phone' => $phone];
            }
        }

        return $rows;
    }

    /** @return array<int, array<int, string>> */
    private static function fromCsv(string $path): array
    {
        $rows = [];
        $handle = fopen($path, 'r');

        if (! $handle) {
            return [];
        }

        // Excel writes a marker at the start of a UTF-8 CSV. Drop it, or the
        // first cell arrives with invisible characters stuck to it.
        $first = fgets($handle);
        rewind($handle);

        if ($first !== false && str_starts_with($first, "\xEF\xBB\xBF")) {
            fseek($handle, 3);
        }

        // Semicolons are normal in CSVs saved on Indian and European systems.
        $separator = ($first !== false && substr_count($first, ';') > substr_count($first, ',')) ? ';' : ',';

        while (($cells = fgetcsv($handle, 0, $separator, '"', '\\')) !== false) {
            $rows[] = array_map(fn ($c) => (string) $c, $cells);
        }

        fclose($handle);

        return $rows;
    }

    /** @return array<int, array<int, string>> */
    private static function fromXlsx(string $path): array
    {
        $zip = new \ZipArchive;

        if ($zip->open($path) !== true) {
            return [];
        }

        // Text in a spreadsheet is usually kept once in a shared list, and
        // the cells just point at it by number.
        $shared = [];
        $sharedXml = $zip->getFromName('xl/sharedStrings.xml');

        if ($sharedXml) {
            $xml = @simplexml_load_string($sharedXml);

            foreach ($xml->si ?? [] as $item) {
                // A cell can be one piece of text, or several runs of it.
                $shared[] = trim(implode('', array_map('strval', $item->xpath('.//text()') ?: [])));
            }
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if (! $sheetXml) {
            return [];
        }

        $xml = @simplexml_load_string($sheetXml);

        if (! $xml) {
            return [];
        }

        $rows = [];

        foreach ($xml->sheetData->row ?? [] as $row) {
            $cells = [];

            foreach ($row->c ?? [] as $cell) {
                $value = (string) $cell->v;

                if ((string) $cell['t'] === 's') {
                    $value = $shared[(int) $value] ?? '';
                } elseif ((string) $cell['t'] === 'inlineStr') {
                    $value = trim((string) $cell->is->t);
                }

                $cells[] = $value;
            }

            $rows[] = $cells;
        }

        return $rows;
    }

    /** True for a row like "Name, Phone" that labels the columns. */
    private static function isHeading(array $cells): bool
    {
        foreach ($cells as $cell) {
            if (in_array(mb_strtolower(trim($cell)), self::HEADINGS, true)) {
                return true;
            }
        }

        return false;
    }
}
