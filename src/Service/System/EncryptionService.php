<?php
declare(strict_types=1);

namespace App\Service\System;

use InvalidArgumentException;
use RuntimeException;

class EncryptionService
{
    private string $key;

    public function __construct(string $key)
    {
        if ($key === '') {
            throw new InvalidArgumentException('Encryption key is required.');
        }

        $this->key = sodium_crypto_generichash($key, '', SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
    }

    public function encrypt(string $data): string
    {
        try {
            $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
            $cipher = sodium_crypto_secretbox($data, $nonce, $this->key);

            return base64_encode($nonce . $cipher);
        } catch (\Throwable $e) {
            throw new RuntimeException('Encryption failed.', 0, $e);
        }
    }

    public function decrypt(string $encryptedData): ?string
    {
        try {
            $decoded = base64_decode($encryptedData, true);

            if ($decoded === false || strlen($decoded) < SODIUM_CRYPTO_SECRETBOX_NONCEBYTES) {
                return null;
            }

            $nonce = substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
            $cipher = substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
            $plain = sodium_crypto_secretbox_open($cipher, $nonce, $this->key);

            if ($plain === false) {
                return null;
            }

            return $plain;
        } catch (\Throwable) {
            return null;
        }
    }

    public function encryptArray(array $data): string
    {
        try {
            $json = json_encode($data, JSON_THROW_ON_ERROR);
            return $this->encrypt($json);
        } catch (\Throwable $e) {
            throw new RuntimeException('Encryption failed.', 0, $e);
        }
    }

    public function decryptArray(string $encryptedData): ?array
    {
        $plain = $this->decrypt($encryptedData);

        if ($plain === null) {
            return null;
        }

        try {
            $decoded = json_decode($plain, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            return null;
        }

        return is_array($decoded) ? $decoded : null;
    }
}
