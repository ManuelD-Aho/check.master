<?php
declare(strict_types=1);

namespace App\Service\System;

use RuntimeException;

class CacheService
{
    private string $cachePath;

    public function __construct(string $cachePath)
    {
        $this->cachePath = rtrim($cachePath, DIRECTORY_SEPARATOR);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $path = $this->getFilePath($key);

        if (!is_file($path)) {
            return $default;
        }

        try {
            $content = file_get_contents($path);

            if ($content === false) {
                return $default;
            }

            $payload = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

            if (!is_array($payload) || !array_key_exists('expires_at', $payload) || !array_key_exists('value', $payload)) {
                $this->delete($key);
                return $default;
            }

            $expiresAt = $payload['expires_at'];

            if ($expiresAt !== null && time() > (int) $expiresAt) {
                $this->delete($key);
                return $default;
            }

            $serialized = (string) $payload['value'];
            $value = @unserialize($serialized, ['allowed_classes' => false]);

            if ($value === false && $serialized !== 'b:0;') {
                return $default;
            }

            return $value;
        } catch (\Throwable) {
            $this->delete($key);
            return $default;
        }
    }

    public function set(string $key, mixed $value, int $ttl = 3600): bool
    {
        try {
            $this->ensureCacheDirectory();

            $payload = [
                'expires_at' => $ttl > 0 ? time() + $ttl : null,
                'value' => serialize($value),
            ];

            $data = json_encode($payload, JSON_THROW_ON_ERROR);
            $path = $this->getFilePath($key);
            $tempPath = $path . '.' . uniqid('', true) . '.tmp';

            if (file_put_contents($tempPath, $data, LOCK_EX) === false) {
                return false;
            }

            if (!rename($tempPath, $path)) {
                @unlink($tempPath);
                return false;
            }

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    public function has(string $key): bool
    {
        $path = $this->getFilePath($key);

        if (!is_file($path)) {
            return false;
        }

        try {
            $content = file_get_contents($path);

            if ($content === false) {
                return false;
            }

            $payload = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

            if (!is_array($payload) || !array_key_exists('expires_at', $payload)) {
                $this->delete($key);
                return false;
            }

            $expiresAt = $payload['expires_at'];

            if ($expiresAt !== null && time() > (int) $expiresAt) {
                $this->delete($key);
                return false;
            }

            return true;
        } catch (\Throwable) {
            $this->delete($key);
            return false;
        }
    }

    public function delete(string $key): bool
    {
        $path = $this->getFilePath($key);

        if (!is_file($path)) {
            return true;
        }

        return unlink($path);
    }

    public function clear(): bool
    {
        if (!is_dir($this->cachePath)) {
            return true;
        }

        $files = glob($this->cachePath . DIRECTORY_SEPARATOR . '*.cache');

        if ($files === false) {
            return false;
        }

        $success = true;

        foreach ($files as $file) {
            if (is_file($file) && !unlink($file)) {
                $success = false;
            }
        }

        return $success;
    }

    public function remember(string $key, callable $callback, int $ttl = 3600): mixed
    {
        if ($this->has($key)) {
            return $this->get($key);
        }

        $value = $callback();
        $this->set($key, $value, $ttl);

        return $value;
    }

    private function ensureCacheDirectory(): void
    {
        if (is_dir($this->cachePath)) {
            return;
        }

        if (!mkdir($this->cachePath, 0775, true) && !is_dir($this->cachePath)) {
            throw new RuntimeException('Unable to create cache directory.');
        }
    }

    private function getFilePath(string $key): string
    {
        return $this->cachePath . DIRECTORY_SEPARATOR . hash('sha256', $key) . '.cache';
    }
}
