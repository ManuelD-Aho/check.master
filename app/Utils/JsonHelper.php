<?php

declare(strict_types=1);

namespace App\Utils;

class JsonHelper
{
    public static function encode(mixed $data, int $options = 0): string
    {
        $json = json_encode($data, $options | JSON_THROW_ON_ERROR);
        return $json;
    }

    public static function decode(string $json, bool $associative = true, int $depth = 512, int $options = 0): mixed
    {
        if (empty($json)) {
            return $associative ? [] : null;
        }
        return json_decode($json, $associative, $depth, $options | JSON_THROW_ON_ERROR);
    }

    public static function validate(string $json): bool
    {
        try {
            json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            return true;
        } catch (\JsonException $e) {
            return false;
        }
    }
}
