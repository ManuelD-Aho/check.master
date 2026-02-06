<?php

declare(strict_types=1);

namespace App\Helper;

final class DateHelper
{
    private const FRENCH_MONTHS = [
        1  => 'janvier',
        2  => 'février',
        3  => 'mars',
        4  => 'avril',
        5  => 'mai',
        6  => 'juin',
        7  => 'juillet',
        8  => 'août',
        9  => 'septembre',
        10 => 'octobre',
        11 => 'novembre',
        12 => 'décembre',
    ];

    private function __construct()
    {
    }

    public static function format(\DateTimeInterface $date, string $format = 'd/m/Y'): string
    {
        return $date->format($format);
    }

    public static function formatFull(\DateTimeInterface $date): string
    {
        $day   = $date->format('d');
        $month = (int) $date->format('m');
        $year  = $date->format('Y');

        return $day . ' ' . self::FRENCH_MONTHS[$month] . ' ' . $year;
    }

    public static function formatTime(\DateTimeInterface $date): string
    {
        return $date->format('H:i');
    }

    public static function diffInDays(\DateTimeInterface $start, \DateTimeInterface $end): int
    {
        $diff = $start->diff($end);

        return (int) $diff->days;
    }

    public static function diffInMonths(\DateTimeInterface $start, \DateTimeInterface $end): int
    {
        $diff = $start->diff($end);

        return ($diff->y * 12) + $diff->m;
    }

    public static function addDays(\DateTimeInterface $date, int $days): \DateTimeImmutable
    {
        $immutable = $date instanceof \DateTimeImmutable
            ? $date
            : \DateTimeImmutable::createFromMutable($date);

        return $immutable->modify(sprintf('%+d days', $days));
    }

    public static function addMonths(\DateTimeInterface $date, int $months): \DateTimeImmutable
    {
        $immutable = $date instanceof \DateTimeImmutable
            ? $date
            : \DateTimeImmutable::createFromMutable($date);

        return $immutable->modify(sprintf('%+d months', $months));
    }

    public static function isWeekend(\DateTimeInterface $date): bool
    {
        $dayOfWeek = (int) $date->format('N');

        return $dayOfWeek >= 6;
    }

    public static function isBusinessDay(\DateTimeInterface $date): bool
    {
        return !self::isWeekend($date);
    }

    public static function isInRange(
        \DateTimeInterface $date,
        \DateTimeInterface $start,
        \DateTimeInterface $end,
    ): bool {
        return $date >= $start && $date <= $end;
    }

    public static function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }

    public static function today(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('today');
    }

    public static function parse(string $date, string $format = 'Y-m-d'): ?\DateTimeImmutable
    {
        $parsed = \DateTimeImmutable::createFromFormat($format, $date);

        return $parsed !== false ? $parsed : null;
    }
}
