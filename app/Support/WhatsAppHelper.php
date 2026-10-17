<?php

namespace App\Support;

class WhatsAppHelper
{
    /**
     * Normalize an Indonesian phone number.
     *
     * Converts:
     * - '081234567890' -> '6281234567890' (or '+6281234567890' if $withPlus)
     * - '81234567890' -> '6281234567890'
     * - '+6281234567890' -> '6281234567890'
     * - '6281234567890' -> '6281234567890'
     * - '0812-3456-7890' -> '6281234567890'
     */
    public static function normalizeIndonesianPhone(?string $phone, bool $withPlus = false): ?string
    {
        if ($phone === null || trim($phone) === '') {
            return null;
        }

        $digits = preg_replace('/[^0-9]/', '', $phone);
        if ($digits === '' || $digits === null) {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '62'.$digits;
        }

        return $withPlus ? '+'.$digits : $digits;
    }

    /**
     * Get the national number portion without country code or leading 0.
     * Suitable for inputs paired with a static '+62' prefix badge.
     *
     * Converts:
     * - '+6281234567890' -> '81234567890'
     * - '6281234567890' -> '81234567890'
     * - '081234567890' -> '81234567890'
     */
    public static function formatNationalNumber(?string $phone): string
    {
        if ($phone === null || trim($phone) === '') {
            return '';
        }

        $digits = preg_replace('/[^0-9]/', '', $phone);
        if ($digits === '' || $digits === null) {
            return '';
        }

        if (str_starts_with($digits, '62')) {
            return substr($digits, 2);
        }

        if (str_starts_with($digits, '0')) {
            return substr($digits, 1);
        }

        return $digits;
    }

    /**
     * Build an official WhatsApp Click to Chat URL.
     *
     * URL Format: https://wa.me/628xxxxxxxxxx?text=...
     */
    public static function buildUrl(?string $phone, string $message = ''): string
    {
        $normalized = self::normalizeIndonesianPhone($phone, false);
        if ($normalized === null || $normalized === '') {
            return '#';
        }

        $url = 'https://wa.me/'.$normalized;

        if ($message !== '') {
            $url .= '?text='.urlencode($message);
        }

        return $url;
    }
}

