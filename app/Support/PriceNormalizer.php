<?php

namespace App\Support;

/**
 * Normalizes human-entered prices (Portuguese/European or plain formats) into a
 * canonical decimal string with a dot separator. Single source of truth shared
 * by the backoffice form (MachineController) and the public catalogue price
 * filter (CatalogController).
 */
class PriceNormalizer
{
    /**
     * Examples:
     *   "1200"      -> "1200"
     *   "1200.50"   -> "1200.50"
     *   "1200,50"   -> "1200.50"
     *   "1.200,50"  -> "1200.50"
     *   "1 200,50"  -> "1200.50"
     *
     * Returns null for empty or invalid input (e.g. "abc", "12.34.56").
     */
    public static function normalize(string $value): ?string
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        $normalized = preg_replace('/[^\d,.\s]/u', '', $value);
        if ($normalized === null) {
            return null;
        }

        $normalized = preg_replace('/\s+/u', '', $normalized);
        if ($normalized === null || $normalized === '') {
            return null;
        }

        $lastComma = strrpos($normalized, ',');
        $lastDot = strrpos($normalized, '.');

        $decimalSeparator = null;

        if ($lastComma !== false && $lastDot !== false) {
            // Whichever appears last is the decimal separator.
            $decimalSeparator = $lastComma > $lastDot ? ',' : '.';
        } elseif ($lastComma !== false && self::looksLikeDecimalSeparator($normalized, ',')) {
            $decimalSeparator = ',';
        } elseif ($lastDot !== false && self::looksLikeDecimalSeparator($normalized, '.')) {
            $decimalSeparator = '.';
        }

        if ($decimalSeparator === ',') {
            $normalized = str_replace('.', '', $normalized);
            $normalized = str_replace(',', '.', $normalized);
        } elseif ($decimalSeparator === '.') {
            $normalized = str_replace(',', '', $normalized);
        } else {
            // No decimal separator: treat dots/commas as thousands separators.
            $normalized = str_replace([',', '.'], '', $normalized);
        }

        if (! preg_match('/^\d+(\.\d{1,2})?$/', $normalized)) {
            return null;
        }

        return $normalized;
    }

    /**
     * Convenience wrapper returning a float (or null) for numeric comparisons.
     */
    public static function toFloat(string $value): ?float
    {
        $normalized = self::normalize($value);

        return $normalized === null ? null : (float) $normalized;
    }

    private static function looksLikeDecimalSeparator(string $value, string $separator): bool
    {
        $parts = explode($separator, $value);
        $fraction = end($parts);

        if ($fraction === false) {
            return false;
        }

        $length = strlen($fraction);

        return $length >= 1 && $length <= 2;
    }
}
