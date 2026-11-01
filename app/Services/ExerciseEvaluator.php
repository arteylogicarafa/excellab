<?php
/**
 * Corrección automática de respuestas.
 * Tipos de respuesta: option (id de opción), number (con tolerancia), formula (normalizada) y text.
 * En correct_answer se admiten alternativas separadas por "||".
 */
class ExerciseEvaluator
{
    public static function normalizeFormula(string $s): string
    {
        $s = str_replace(['“', '”', '«', '»'], '"', $s);
        $s = preg_replace('/\s+/u', '', $s);
        $s = mb_strtoupper($s, 'UTF-8');
        return str_replace(';', ',', $s);
    }

    public static function normalizeText(string $s): string
    {
        $s = mb_strtolower(trim(preg_replace('/\s+/u', ' ', $s)), 'UTF-8');
        return strtr($s, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n']);
    }

    public static function parseNumber(string $s): ?float
    {
        $s = trim(str_replace(['%', '$', ' ', "\xC2\xA0"], '', $s));
        if ($s === '') {
            return null;
        }
        if (preg_match('/^-?\d{1,3}(\.\d{3})+(,\d+)?$/', $s)) {      // 1.234,56
            $s = str_replace(['.', ','], ['', '.'], $s);
        } elseif (substr_count($s, ',') === 1 && !str_contains($s, '.')) { // 12,5
            $s = str_replace(',', '.', $s);
        } elseif (preg_match('/^-?\d{1,3}(,\d{3})+(\.\d+)?$/', $s)) {  // 1,234.56
            $s = str_replace(',', '', $s);
        }
        return is_numeric($s) ? (float)$s : null;
    }

    /** Compara una respuesta libre con la respuesta esperada. */
    public static function matches(string $kind, string $answer, string $expected, float $tolerance = 0.01): bool
    {
        $alternatives = array_filter(array_map('trim', explode('||', $expected)), fn($x) => $x !== '');
        foreach ($alternatives as $alt) {
            switch ($kind) {
                case 'number':
                    $a = self::parseNumber($answer);
                    $b = self::parseNumber($alt);
                    if ($a !== null && $b !== null && abs($a - $b) <= $tolerance + 1e-9) {
                        return true;
                    }
                    break;
                case 'formula':
                    if (self::normalizeFormula($answer) === self::normalizeFormula($alt)) {
                        return true;
                    }
                    break;
                default:
                    if (self::normalizeText($answer) === self::normalizeText($alt)) {
                        return true;
                    }
            }
        }
        return false;
    }

    /**
     * @param array $ex      fila de exercises
     * @param array $options opciones del ejercicio (exercise_options)
     * @return array{0:bool,1:string} [esCorrecta, textoRespuestaCorrecta]
     */
    public static function check(array $ex, string $answer, array $options = []): array
    {
        $answer = trim($answer);
        if ($ex['answer_kind'] === 'option') {
            $correct = null;
            $ok = false;
            foreach ($options as $o) {
                if ($o['is_correct']) {
                    $correct = $o['label'];
                    if ((string)$o['id'] === $answer) {
                        $ok = true;
                    }
                }
            }
            return [$ok, (string)$correct];
        }
        $first = trim(explode('||', (string)$ex['correct_answer'])[0]);
        if ($answer === '') {
            return [false, $first];
        }
        return [self::matches($ex['answer_kind'], $answer, (string)$ex['correct_answer'], (float)$ex['tolerance']), $first];
    }
}
