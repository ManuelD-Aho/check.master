<?php

declare(strict_types=1);

namespace App\Helper;

final class UrlHelper
{
    private function __construct()
    {
    }

    public static function generate(string $path, array $params = []): string
    {
        $path = '/' . ltrim($path, '/');

        if (count($params) === 0) {
            return $path;
        }

        return $path . '?' . http_build_query($params);
    }

    public static function current(): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $uri    = $_SERVER['REQUEST_URI'] ?? '/';

        return $scheme . '://' . $host . $uri;
    }

    public static function isAbsolute(string $url): bool
    {
        return str_starts_with($url, 'http://') || str_starts_with($url, 'https://');
    }

    public static function addQueryParam(string $url, string $key, string $value): string
    {
        $parsed = parse_url($url);
        $query  = [];

        if (isset($parsed['query'])) {
            parse_str($parsed['query'], $query);
        }

        $query[$key] = $value;

        $base = '';

        if (isset($parsed['scheme'])) {
            $base .= $parsed['scheme'] . '://';
        }

        if (isset($parsed['host'])) {
            $base .= $parsed['host'];
        }

        if (isset($parsed['port'])) {
            $base .= ':' . $parsed['port'];
        }

        $base .= $parsed['path'] ?? '';

        return $base . '?' . http_build_query($query);
    }

    public static function basePath(): string
    {
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

        return rtrim(dirname($scriptName), '/');
    }

    public static function asset(string $path): string
    {
        $path = ltrim($path, '/');

        return '/assets/' . $path;
    }

    public static function redirect(string $url): never
    {
        header('Location: ' . $url);

        exit;
    }
}
