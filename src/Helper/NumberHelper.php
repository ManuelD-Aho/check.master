<?php

declare(strict_types=1);

namespace App\Helper;

final class NumberHelper
{
    private const UNITS = [
        0  => 'zéro',
        1  => 'un',
        2  => 'deux',
        3  => 'trois',
        4  => 'quatre',
        5  => 'cinq',
        6  => 'six',
        7  => 'sept',
        8  => 'huit',
        9  => 'neuf',
        10 => 'dix',
        11 => 'onze',
        12 => 'douze',
        13 => 'treize',
        14 => 'quatorze',
        15 => 'quinze',
        16 => 'seize',
    ];

    private const TENS = [
        2 => 'vingt',
        3 => 'trente',
        4 => 'quarante',
        5 => 'cinquante',
        6 => 'soixante',
        7 => 'soixante',
        8 => 'quatre-vingt',
        9 => 'quatre-vingt',
    ];

    private function __construct()
    {
    }

    public static function format(float $number, int $decimals = 2): string
    {
        return number_format($number, $decimals, ',', ' ');
    }

    public static function formatMoney(float $amount, string $currency = 'FCFA'): string
    {
        return self::format($amount) . ' ' . $currency;
    }

    public static function toWords(int $number, string $lang = 'fr'): string
    {
        if ($number < 0) {
            return 'moins ' . self::toWords(-$number, $lang);
        }

        if ($number === 0) {
            return self::UNITS[0];
        }

        return trim(self::convertChunk($number));
    }

    public static function round(float $number, int $precision = 2): float
    {
        return round($number, $precision);
    }

    public static function percentage(float $value, float $total): float
    {
        if ($total === 0.0) {
            return 0.0;
        }

        return ($value / $total) * 100;
    }

    public static function average(array $numbers): float
    {
        if (count($numbers) === 0) {
            return 0.0;
        }

        return array_sum($numbers) / count($numbers);
    }

    public static function isInRange(float $value, float $min, float $max): bool
    {
        return $value >= $min && $value <= $max;
    }

    private static function convertChunk(int $number): string
    {
        if ($number === 0) {
            return '';
        }

        if ($number >= 1000000) {
            $millions  = intdiv($number, 1000000);
            $remainder = $number % 1000000;

            $result = ($millions === 1)
                ? 'un million'
                : self::convertChunk($millions) . ' millions';

            if ($remainder > 0) {
                $result .= ' ' . self::convertChunk($remainder);
            }

            return $result;
        }

        if ($number >= 1000) {
            $thousands = intdiv($number, 1000);
            $remainder = $number % 1000;

            $result = ($thousands === 1)
                ? 'mille'
                : self::convertChunk($thousands) . ' mille';

            if ($remainder > 0) {
                $result .= ' ' . self::convertChunk($remainder);
            }

            return $result;
        }

        if ($number >= 100) {
            $hundreds  = intdiv($number, 100);
            $remainder = $number % 100;

            $result = ($hundreds === 1)
                ? 'cent'
                : self::UNITS[$hundreds] . ' cent';

            if ($remainder === 0 && $hundreds > 1) {
                $result .= 's';
            } elseif ($remainder > 0) {
                $result .= ' ' . self::convertBelow100($remainder);
            }

            return $result;
        }

        return self::convertBelow100($number);
    }

    private static function convertBelow100(int $number): string
    {
        if ($number <= 16) {
            return self::UNITS[$number];
        }

        if ($number < 20) {
            return 'dix-' . self::UNITS[$number - 10];
        }

        $ten       = intdiv($number, 10);
        $unit      = $number % 10;
        $tenWord   = self::TENS[$ten];

        if ($ten === 7 || $ten === 9) {
            $subNumber = $number - ($ten === 7 ? 60 : 80);

            if ($subNumber === 1 && $ten === 7) {
                return 'soixante et onze';
            }

            return $tenWord . '-' . self::convertBelow100($subNumber);
        }

        if ($unit === 0) {
            return ($ten === 8) ? $tenWord . 's' : $tenWord;
        }

        if ($unit === 1 && $ten !== 8) {
            return $tenWord . ' et un';
        }

        return $tenWord . '-' . self::UNITS[$unit];
    }
}
