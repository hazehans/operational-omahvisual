<?php

namespace App\Support;

use Carbon\Carbon;
use Carbon\CarbonInterface;

class IndonesianHolidays
{
    /**
     * Fixed national holidays across all years (MM-DD => Name).
     *
     * @var array<string, string>
     */
    protected static array $fixedHolidays = [
        '01-01' => 'Tahun Baru Masehi',
        '05-01' => 'Hari Buruh Internasional',
        '06-01' => 'Hari Lahir Pancasila',
        '08-17' => 'Hari Kemerdekaan RI (HUT RI)',
        '12-25' => 'Hari Raya Natal',
    ];

    /**
     * Official Indonesian National Holidays & Cuti Bersama (SKB 3 Menteri).
     *
     * @var array<int, array<string, string>>
     */
    protected static array $yearlyHolidays = [
        2025 => [
            '2025-01-01' => 'Tahun Baru 2025 Masehi',
            '2025-01-27' => 'Isra Mi\'raj Nabi Muhammad SAW',
            '2025-01-28' => 'Cuti Bersama Tahun Baru Imlek',
            '2025-01-29' => 'Tahun Baru Imlek 2576 Kongzili',
            '2025-03-29' => 'Hari Suci Nyepi (Tahun Baru Saka 1947)',
            '2025-03-31' => 'Hari Raya Idul Fitri 1446 H',
            '2025-04-01' => 'Hari Raya Idul Fitri 1446 H',
            '2025-04-02' => 'Cuti Bersama Idul Fitri 1446 H',
            '2025-04-03' => 'Cuti Bersama Idul Fitri 1446 H',
            '2025-04-04' => 'Cuti Bersama Idul Fitri 1446 H',
            '2025-04-07' => 'Cuti Bersama Idul Fitri 1446 H',
            '2025-04-18' => 'Wafat Yesus Kristus (Jumat Agung)',
            '2025-04-20' => 'Hari Paskah',
            '2025-05-01' => 'Hari Buruh Internasional',
            '2025-05-12' => 'Hari Raya Waisak 2569 BE',
            '2025-05-13' => 'Cuti Bersama Hari Raya Waisak',
            '2025-05-29' => 'Kenaikan Yesus Kristus',
            '2025-05-30' => 'Cuti Bersama Kenaikan Yesus Kristus',
            '2025-06-01' => 'Hari Lahir Pancasila',
            '2025-06-06' => 'Hari Raya Idul Adha 1446 H',
            '2025-06-09' => 'Cuti Bersama Idul Adha 1446 H',
            '2025-06-27' => 'Tahun Baru Islam 1447 H (1 Muharram)',
            '2025-08-17' => 'Proklamasi Kemerdekaan RI Ke-80',
            '2025-09-05' => 'Maulid Nabi Muhammad SAW',
            '2025-12-25' => 'Hari Raya Natal',
            '2025-12-26' => 'Cuti Bersama Hari Raya Natal',
        ],
        2026 => [
            '2026-01-01' => 'Tahun Baru 2026 Masehi',
            '2026-01-16' => 'Isra Mi\'raj Nabi Muhammad SAW',
            '2026-02-16' => 'Cuti Bersama Tahun Baru Imlek',
            '2026-02-17' => 'Tahun Baru Imlek 2577 Kongzili',
            '2026-03-20' => 'Hari Suci Nyepi (Tahun Baru Saka 1948)',
            '2026-03-21' => 'Hari Raya Idul Fitri 1447 H (Hari 1)',
            '2026-03-22' => 'Hari Raya Idul Fitri 1447 H (Hari 2)',
            '2026-03-23' => 'Cuti Bersama Idul Fitri 1447 H',
            '2026-03-24' => 'Cuti Bersama Idul Fitri 1447 H',
            '2026-04-03' => 'Wafat Yesus Kristus (Jumat Agung)',
            '2026-04-05' => 'Kebangkitan Yesus Kristus (Hari Paskah)',
            '2026-05-01' => 'Hari Buruh Internasional',
            '2026-05-14' => 'Kenaikan Yesus Kristus',
            '2026-05-27' => 'Hari Raya Idul Adha 1447 H',
            '2026-05-31' => 'Hari Raya Waisak 2570 BE',
            '2026-06-01' => 'Hari Lahir Pancasila',
            '2026-06-16' => 'Tahun Baru Islam 1448 H (1 Muharram)',
            '2026-08-17' => 'Proklamasi Kemerdekaan RI Ke-81',
            '2026-08-25' => 'Maulid Nabi Muhammad SAW',
            '2026-12-25' => 'Hari Raya Natal',
            '2026-12-26' => 'Cuti Bersama Hari Raya Natal',
        ],
        2027 => [
            '2027-01-01' => 'Tahun Baru 2027 Masehi',
            '2027-01-06' => 'Isra Mi\'raj Nabi Muhammad SAW',
            '2027-02-06' => 'Tahun Baru Imlek 2578 Kongzili',
            '2027-03-09' => 'Hari Suci Nyepi (Tahun Baru Saka 1949)',
            '2027-03-10' => 'Hari Raya Idul Fitri 1448 H',
            '2027-03-11' => 'Hari Raya Idul Fitri 1448 H',
            '2027-03-26' => 'Wafat Yesus Kristus (Jumat Agung)',
            '2027-03-28' => 'Hari Paskah',
            '2027-05-01' => 'Hari Buruh Internasional',
            '2027-05-06' => 'Kenaikan Yesus Kristus',
            '2027-05-16' => 'Hari Raya Idul Adha 1448 H',
            '2027-05-20' => 'Hari Raya Waisak 2571 BE',
            '2027-06-01' => 'Hari Lahir Pancasila',
            '2027-06-06' => 'Tahun Baru Islam 1449 H',
            '2027-08-15' => 'Maulid Nabi Muhammad SAW',
            '2027-08-17' => 'Proklamasi Kemerdekaan RI Ke-82',
            '2027-12-25' => 'Hari Raya Natal',
        ],
    ];

    /**
     * Get holiday name for a given date string (YYYY-MM-DD) or Carbon instance.
     */
    public static function getForDate(string|CarbonInterface $date): ?string
    {
        $carbon = is_string($date) ? Carbon::parse($date) : $date;
        $dateStr = $carbon->toDateString();
        $year = $carbon->year;
        $mmDd = $carbon->format('m-d');

        if (isset(self::$yearlyHolidays[$year][$dateStr])) {
            return self::$yearlyHolidays[$year][$dateStr];
        }

        if (isset(self::$fixedHolidays[$mmDd])) {
            return self::$fixedHolidays[$mmDd];
        }

        return null;
    }

    /**
     * Check if a given date is a national holiday.
     */
    public static function isHoliday(string|CarbonInterface $date): bool
    {
        return self::getForDate($date) !== null;
    }

    /**
     * Get all holidays in a given year and month.
     *
     * @return array<string, string> [ 'YYYY-MM-DD' => 'Holiday Name' ]
     */
    public static function getForMonth(int $year, int $month): array
    {
        $result = [];
        $monthStr = str_pad((string) $month, 2, '0', STR_PAD_LEFT);

        // Check yearly
        if (isset(self::$yearlyHolidays[$year])) {
            foreach (self::$yearlyHolidays[$year] as $date => $name) {
                if (str_starts_with($date, "{$year}-{$monthStr}")) {
                    $result[$date] = $name;
                }
            }
        }

        // Check fixed
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dayStr = str_pad((string) $d, 2, '0', STR_PAD_LEFT);
            $mmDd = "{$monthStr}-{$dayStr}";
            $fullDate = "{$year}-{$mmDd}";

            if (isset(self::$fixedHolidays[$mmDd]) && ! isset($result[$fullDate])) {
                $result[$fullDate] = self::$fixedHolidays[$mmDd];
            }
        }

        ksort($result);

        return $result;
    }
}
