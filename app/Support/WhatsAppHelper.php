<?php

namespace App\Support;

class WhatsAppHelper
{
    /**
     * Curated list of international countries with dial codes and flag emojis.
     */
    public const COUNTRIES = [
        ['name' => 'Indonesia', 'code' => 'ID', 'dial_code' => '+62', 'flag' => '🇮🇩'],
        ['name' => 'Malaysia', 'code' => 'MY', 'dial_code' => '+60', 'flag' => '🇲🇾'],
        ['name' => 'Singapura', 'code' => 'SG', 'dial_code' => '+65', 'flag' => '🇸🇬'],
        ['name' => 'Filipina', 'code' => 'PH', 'dial_code' => '+63', 'flag' => '🇵🇭'],
        ['name' => 'Thailand', 'code' => 'TH', 'dial_code' => '+66', 'flag' => '🇹🇭'],
        ['name' => 'Vietnam', 'code' => 'VN', 'dial_code' => '+84', 'flag' => '🇻🇳'],
        ['name' => 'Brunei Darussalam', 'code' => 'BN', 'dial_code' => '+673', 'flag' => '🇧🇳'],
        ['name' => 'Kamboja', 'code' => 'KH', 'dial_code' => '+855', 'flag' => '🇰🇭'],
        ['name' => 'Laos', 'code' => 'LA', 'dial_code' => '+856', 'flag' => '🇱🇦'],
        ['name' => 'Myanmar', 'code' => 'MM', 'dial_code' => '+95', 'flag' => '🇲🇲'],
        ['name' => 'Timor Leste', 'code' => 'TL', 'dial_code' => '+670', 'flag' => '🇹🇱'],
        ['name' => 'Australia', 'code' => 'AU', 'dial_code' => '+61', 'flag' => '🇦🇺'],
        ['name' => 'Selandia Baru', 'code' => 'NZ', 'dial_code' => '+64', 'flag' => '🇳🇿'],
        ['name' => 'Jepang', 'code' => 'JP', 'dial_code' => '+81', 'flag' => '🇯🇵'],
        ['name' => 'Korea Selatan', 'code' => 'KR', 'dial_code' => '+82', 'flag' => '🇰🇷'],
        ['name' => 'Tiongkok', 'code' => 'CN', 'dial_code' => '+86', 'flag' => '🇨🇳'],
        ['name' => 'Hong Kong', 'code' => 'HK', 'dial_code' => '+852', 'flag' => '🇭🇰'],
        ['name' => 'Taiwan', 'code' => 'TW', 'dial_code' => '+886', 'flag' => '🇹🇼'],
        ['name' => 'India', 'code' => 'IN', 'dial_code' => '+91', 'flag' => '🇮🇳'],
        ['name' => 'Arab Saudi', 'code' => 'SA', 'dial_code' => '+966', 'flag' => '🇸🇦'],
        ['name' => 'Uni Emirat Arab', 'code' => 'AE', 'dial_code' => '+971', 'flag' => '🇦🇪'],
        ['name' => 'Qatar', 'code' => 'QA', 'dial_code' => '+974', 'flag' => '🇶🇦'],
        ['name' => 'Amerika Serikat', 'code' => 'US', 'dial_code' => '+1', 'flag' => '🇺🇸'],
        ['name' => 'Kanada', 'code' => 'CA', 'dial_code' => '+1', 'flag' => '🇨🇦'],
        ['name' => 'Inggris (UK)', 'code' => 'GB', 'dial_code' => '+44', 'flag' => '🇬🇧'],
        ['name' => 'Jerman', 'code' => 'DE', 'dial_code' => '+49', 'flag' => '🇩🇪'],
        ['name' => 'Belanda', 'code' => 'NL', 'dial_code' => '+31', 'flag' => '🇳🇱'],
        ['name' => 'Prancis', 'code' => 'FR', 'dial_code' => '+33', 'flag' => '🇫🇷'],
        ['name' => 'Italia', 'code' => 'IT', 'dial_code' => '+39', 'flag' => '🇮🇹'],
        ['name' => 'Spanyol', 'code' => 'ES', 'dial_code' => '+34', 'flag' => '🇪🇸'],
        ['name' => 'Swiss', 'code' => 'CH', 'dial_code' => '+41', 'flag' => '🇨🇭'],
        ['name' => 'Turki', 'code' => 'TR', 'dial_code' => '+90', 'flag' => '🇹🇷'],
        ['name' => 'Rusia', 'code' => 'RU', 'dial_code' => '+7', 'flag' => '🇷🇺'],
    ];

    /**
     * Parse any phone number into its country, dial code, national number, and full phone.
     *
     * @return array{country: array{name: string, code: string, dial_code: string, flag: string}, dial_code: string, national_number: string, full_phone: string}
     */
    public static function parsePhone(?string $phone): array
    {
        $default = [
            'country' => self::COUNTRIES[0],
            'dial_code' => '+62',
            'national_number' => '',
            'full_phone' => '',
        ];

        if ($phone === null || trim($phone) === '') {
            return $default;
        }

        $trimmed = trim($phone);

        // Check if starts with '+'
        if (str_starts_with($trimmed, '+')) {
            $digits = preg_replace('/[^0-9]/', '', $trimmed);
            if ($digits === '' || $digits === null) {
                return $default;
            }

            // Sort countries by dial code length descending (e.g. +673 before +67)
            $sortedCountries = self::COUNTRIES;
            usort($sortedCountries, function ($a, $b) {
                return strlen($b['dial_code']) <=> strlen($a['dial_code']);
            });

            foreach ($sortedCountries as $c) {
                $cDigits = preg_replace('/[^0-9]/', '', $c['dial_code']);
                if (str_starts_with($digits, $cDigits)) {
                    $national = substr($digits, strlen($cDigits));

                    return [
                        'country' => $c,
                        'dial_code' => $c['dial_code'],
                        'national_number' => $national,
                        'full_phone' => '+'.$cDigits.$national,
                    ];
                }
            }

            // Fallback for unknown country with '+'
            return [
                'country' => self::COUNTRIES[0],
                'dial_code' => '+62',
                'national_number' => $digits,
                'full_phone' => '+'.$digits,
            ];
        }

        // Doesn't start with '+'. Check standard numbers
        $digits = preg_replace('/[^0-9]/', '', $trimmed);
        if ($digits === '' || $digits === null) {
            return $default;
        }

        if (str_starts_with($digits, '62')) {
            return [
                'country' => self::COUNTRIES[0],
                'dial_code' => '+62',
                'national_number' => substr($digits, 2),
                'full_phone' => '+'.$digits,
            ];
        }

        if (str_starts_with($digits, '0')) {
            $national = substr($digits, 1);

            return [
                'country' => self::COUNTRIES[0],
                'dial_code' => '+62',
                'national_number' => $national,
                'full_phone' => '+62'.$national,
            ];
        }

        // Raw national number default to Indonesia
        return [
            'country' => self::COUNTRIES[0],
            'dial_code' => '+62',
            'national_number' => $digits,
            'full_phone' => '+62'.$digits,
        ];
    }

    /**
     * Normalize an international or local phone number.
     *
     * @param  string|null  $selectedDialCode  e.g. '+62', '+65', '+60'
     */
    public static function normalizePhone(?string $phone, ?string $selectedDialCode = null, bool $withPlus = false): ?string
    {
        if ($phone === null || trim($phone) === '') {
            return null;
        }

        $trimmed = trim($phone);

        // If explicitly starts with '+', use parsed phone
        if (str_starts_with($trimmed, '+')) {
            $parsed = self::parsePhone($trimmed);
            $cleanDigits = preg_replace('/[^0-9]/', '', $parsed['full_phone']);

            if ($cleanDigits === '' || $cleanDigits === null) {
                return null;
            }

            return $withPlus ? '+'.$cleanDigits : $cleanDigits;
        }

        $digits = preg_replace('/[^0-9]/', '', $trimmed);
        if ($digits === '' || $digits === null) {
            return null;
        }

        // If selectedDialCode is provided
        if ($selectedDialCode !== null && trim($selectedDialCode) !== '') {
            $dialDigits = preg_replace('/[^0-9]/', '', $selectedDialCode);

            if (str_starts_with($digits, $dialDigits)) {
                $finalDigits = $digits;
            } elseif (str_starts_with($digits, '0')) {
                $finalDigits = $dialDigits.substr($digits, 1);
            } else {
                $finalDigits = $dialDigits.$digits;
            }

            return $withPlus ? '+'.$finalDigits : $finalDigits;
        }

        // Default to Indonesian normalization if no dial code specified
        return self::normalizeIndonesianPhone($phone, $withPlus);
    }

    /**
     * Normalize an Indonesian phone number (backward compatible).
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
     * Suitable for inputs paired with a country code selector badge.
     */
    public static function formatNationalNumber(?string $phone): string
    {
        if ($phone === null || trim($phone) === '') {
            return '';
        }

        return self::parsePhone($phone)['national_number'];
    }

    /**
     * Build an official WhatsApp Click to Chat URL for any international number.
     *
     * URL Format: https://wa.me/<country_code><national_number>?text=...
     */
    public static function buildUrl(?string $phone, string $message = ''): string
    {
        if ($phone === null || trim($phone) === '') {
            return '#';
        }

        $parsed = self::parsePhone($phone);
        $digits = preg_replace('/[^0-9]/', '', $parsed['full_phone']);

        if ($digits === '' || $digits === null) {
            return '#';
        }

        $url = 'https://wa.me/'.$digits;

        if ($message !== '') {
            $url .= '?text='.urlencode($message);
        }

        return $url;
    }
}
