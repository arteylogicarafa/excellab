<?php
/** Generador mínimo de archivos .xlsx (plantillas descargables) usando ZipArchive. */
class XlsxWriter
{
    private static function xml(string $s): string
    {
        return htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    /** @return string contenido binario del .xlsx */
    public static function build(array $headers, array $rows, string $sheetName = 'Datos'): string
    {
        $sheetName = mb_substr(preg_replace('/[\\\\\/\?\*\[\]:]/u', ' ', $sheetName), 0, 31) ?: 'Datos';
        $all = array_merge([$headers], $rows);
        $sheetRows = '';
        $maxLen = [];
        foreach ($all as $r => $row) {
            $cells = '';
            foreach (array_values($row) as $c => $val) {
                $ref = col_letter($c) . ($r + 1);
                $maxLen[$c] = max($maxLen[$c] ?? 8, min(40, mb_strlen((string)$val) + 3));
                $style = $r === 0 ? ' s="1"' : '';
                if ($val === '' || $val === null) {
                    $cells .= $r === 0 ? "<c r=\"$ref\"$style/>" : '';
                } elseif (is_string($val) && $val[0] === '=') {
                    $cells .= "<c r=\"$ref\"$style><f>" . self::xml(substr($val, 1)) . '</f></c>';
                } elseif (is_int($val) || is_float($val)) {
                    $cells .= "<c r=\"$ref\"$style><v>$val</v></c>";
                } else {
                    $cells .= "<c r=\"$ref\" t=\"inlineStr\"$style><is><t>" . self::xml((string)$val) . '</t></is></c>';
                }
            }
            $sheetRows .= '<row r="' . ($r + 1) . '">' . $cells . '</row>';
        }
        $cols = '';
        foreach ($maxLen as $c => $w) {
            $cols .= '<col min="' . ($c + 1) . '" max="' . ($c + 1) . '" width="' . $w . '" customWidth="1"/>';
        }
        $files = [
            '[Content_Types].xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/></Types>',
            '_rels/.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>',
            'xl/workbook.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="' . self::xml($sheetName) . '" sheetId="1" r:id="rId1"/></sheets></workbook>',
            'xl/_rels/workbook.xml.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/></Relationships>',
            'xl/styles.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font></fonts><fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FF217346"/></patternFill></fill></fills><borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders><cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs><cellXfs count="2"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/><xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1"/></cellXfs></styleSheet>',
            'xl/worksheets/sheet1.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><cols>' . $cols . '</cols><sheetData>' . $sheetRows . '</sheetData></worksheet>',
        ];
        $tmp = tempnam(sys_get_temp_dir(), 'xl');
        $zip = new ZipArchive();
        $zip->open($tmp, ZipArchive::OVERWRITE);
        foreach ($files as $name => $content) {
            $zip->addFromString($name, $content);
        }
        $zip->close();
        $bin = file_get_contents($tmp);
        @unlink($tmp);
        return $bin;
    }
}
