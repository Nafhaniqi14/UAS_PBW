<?php
class ExcelExport {
    private string $title;
    private array  $headers;
    private array  $rows;
    private string $headerColor;
    private array  $strings     = [];
    private array  $stringIndex = [];

    public function __construct(string $title, array $headers, array $rows, string $headerColor = 'FF4472C4') {
        $this->title       = $title;
        $this->headers     = $headers;
        $this->rows        = $rows;
        $this->headerColor = $headerColor;
    }

    public function download(string $filename): void {
        $xlsx = $this->build();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($xlsx));
        header('Pragma: no-cache');
        header('Expires: 0');
        echo $xlsx;
        exit;
    }

    private function addString(string $s): int {
        if (isset($this->stringIndex[$s])) return $this->stringIndex[$s];
        $idx = count($this->strings);
        $this->strings[]       = $s;
        $this->stringIndex[$s] = $idx;
        return $idx;
    }

    private function build(): string {
        // Pre-populate shared strings
        foreach ($this->headers as $h) $this->addString((string)$h);
        foreach ($this->rows as $row) {
            foreach (array_values($row) as $cell) {
                if (!$this->isNumericCell($cell)) $this->addString((string)$cell);
            }
        }

        // Hitung auto-width kolom
        $colWidths = array_map('mb_strlen', $this->headers);
        foreach ($this->rows as $row) {
            foreach (array_values($row) as $ci => $cell) {
                $len = mb_strlen((string)$cell);
                if (!isset($colWidths[$ci]) || $len > $colWidths[$ci]) $colWidths[$ci] = $len;
            }
        }
        $colWidths = array_map(fn($w) => min(max($w + 4, 12), 60), $colWidths);

        $tmpDir = sys_get_temp_dir() . '/xlsx_' . uniqid();
        mkdir($tmpDir);
        mkdir("$tmpDir/_rels");
        mkdir("$tmpDir/xl");
        mkdir("$tmpDir/xl/_rels");
        mkdir("$tmpDir/xl/worksheets");

        file_put_contents("$tmpDir/[Content_Types].xml",        $this->contentTypes());
        file_put_contents("$tmpDir/_rels/.rels",                $this->rels());
        file_put_contents("$tmpDir/xl/workbook.xml",            $this->workbook());
        file_put_contents("$tmpDir/xl/_rels/workbook.xml.rels", $this->workbookRels());
        file_put_contents("$tmpDir/xl/styles.xml",              $this->styles());
        file_put_contents("$tmpDir/xl/sharedStrings.xml",       $this->sharedStrings());
        file_put_contents("$tmpDir/xl/worksheets/sheet1.xml",   $this->sheet($colWidths));

        $outFile = $tmpDir . '/output.xlsx';
        $zip = new ZipArchive();
        $zip->open($outFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $this->addDirToZip($zip, $tmpDir, $tmpDir);
        $zip->close();

        $data = file_get_contents($outFile);
        $this->rmDir($tmpDir);
        return $data;
    }

    private function sharedStrings(): string {
        $count = count($this->strings);
        $xml   = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
               . '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . $count . '" uniqueCount="' . $count . '">';
        foreach ($this->strings as $s) {
            $xml .= '<si><t xml:space="preserve">' . htmlspecialchars($s, ENT_XML1, 'UTF-8') . '</t></si>';
        }
        return $xml . '</sst>';
    }

    private function sheet(array $colWidths): string {
        $cols = '';
        foreach ($colWidths as $i => $w) {
            $cols .= '<col min="' . ($i+1) . '" max="' . ($i+1) . '" width="' . $w . '" customWidth="1" bestFit="1"/>';
        }

        // Header row — style 1
        $xmlRows = '<row r="1" ht="20" customHeight="1">';
        foreach ($this->headers as $ci => $h) {
            $col  = $this->colLetter($ci);
            $sidx = $this->addString((string)$h);
            $xmlRows .= '<c r="' . $col . '1" t="s" s="1"><v>' . $sidx . '</v></c>';
        }
        $xmlRows .= '</row>';

        // Data rows — style 2
        foreach ($this->rows as $ri => $row) {
            $rowNum   = $ri + 2;
            $xmlRows .= '<row r="' . $rowNum . '">';
            foreach (array_values($row) as $ci => $cell) {
                $col = $this->colLetter($ci);
                if ($this->isNumericCell($cell)) {
                    $xmlRows .= '<c r="' . $col . $rowNum . '" s="2"><v>' . htmlspecialchars((string)$cell, ENT_XML1) . '</v></c>';
                } else {
                    $sidx    = $this->addString((string)$cell);
                    $xmlRows .= '<c r="' . $col . $rowNum . '" t="s" s="2"><v>' . $sidx . '</v></c>';
                }
            }
            $xmlRows .= '</row>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
             . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
             . '<sheetViews><sheetView workbookViewId="0" tabSelected="1"><selection activeCell="A1"/></sheetView></sheetViews>'
             . '<sheetFormatPr defaultRowHeight="15" customHeight="1"/>'
             . '<cols>' . $cols . '</cols>'
             . '<sheetData>' . $xmlRows . '</sheetData>'
             . '<pageSetup orientation="landscape"/>'
             . '</worksheet>';
    }

    private function styles(): string {
        $hc = $this->headerColor;
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
             . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
             . '<fonts count="2">'
             .   '<font><sz val="11"/><name val="Calibri"/></font>'
             .   '<font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>'
             . '</fonts>'
             . '<fills count="3">'
             .   '<fill><patternFill patternType="none"/></fill>'
             .   '<fill><patternFill patternType="gray125"/></fill>'
             .   '<fill><patternFill patternType="solid"><fgColor rgb="' . $hc . '"/><bgColor indexed="64"/></patternFill></fill>'
             . '</fills>'
             . '<borders count="2">'
             .   '<border><left/><right/><top/><bottom/><diagonal/></border>'
             .   '<border>'
             .     '<left style="thin"><color rgb="FF000000"/></left>'
             .     '<right style="thin"><color rgb="FF000000"/></right>'
             .     '<top style="thin"><color rgb="FF000000"/></top>'
             .     '<bottom style="thin"><color rgb="FF000000"/></bottom>'
             .     '<diagonal/>'
             .   '</border>'
             . '</borders>'
             . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
             . '<cellXfs count="3">'
             .   '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
             .   '<xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1">'
             .     '<alignment horizontal="center" vertical="center" wrapText="0"/>'
             .   '</xf>'
             .   '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1">'
             .     '<alignment vertical="center" wrapText="0"/>'
             .   '</xf>'
             . '</cellXfs>'
             . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
             . '</styleSheet>';
    }

    /** Angka murni (bukan no HP / kode yang diawali 0 atau +) */
    private function isNumericCell(mixed $cell): bool {
        if ($cell === '' || $cell === null) return false;
        $s = (string)$cell;
        // Jika diawali 0 atau + → perlakukan sebagai teks (no HP, kode, dsb)
        if (str_starts_with($s, '0') || str_starts_with($s, '+')) return false;
        return is_numeric($cell);
    }

    private function colLetter(int $idx): string {
        $letters = '';
        $idx++;
        while ($idx > 0) {
            $mod     = ($idx - 1) % 26;
            $letters = chr(65 + $mod) . $letters;
            $idx     = (int)(($idx - $mod) / 26);
        }
        return $letters;
    }

    private function contentTypes(): string {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
             . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
             . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
             . '<Default Extension="xml" ContentType="application/xml"/>'
             . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
             . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
             . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
             . '<Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>'
             . '</Types>';
    }

    private function rels(): string {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
             . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
             . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
             . '</Relationships>';
    }

    private function workbook(): string {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
             . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
             . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
             . '<sheets><sheet name="' . htmlspecialchars($this->title, ENT_XML1, 'UTF-8') . '" sheetId="1" r:id="rId1"/></sheets>'
             . '</workbook>';
    }

    private function workbookRels(): string {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
             . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
             . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
             . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
             . '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>'
             . '</Relationships>';
    }

    private function addDirToZip(ZipArchive $zip, string $dir, string $base): void {
        $iter = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        foreach ($iter as $file) {
            if ($file->isDir()) continue;
            $path = $file->getRealPath();
            if (str_ends_with($path, 'output.xlsx')) continue;
            $zip->addFile($path, substr($path, strlen($base) + 1));
        }
    }

    private function rmDir(string $dir): void {
        $items = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($items as $item) {
            $item->isDir() ? rmdir($item->getRealPath()) : unlink($item->getRealPath());
        }
        rmdir($dir);
    }
}
