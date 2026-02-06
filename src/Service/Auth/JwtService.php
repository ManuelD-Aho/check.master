<?php
declare(strict_types=1);

namespace App\Service\Auth;

use InvalidArgumentException;
use Throwable;

class JwtService
{
    private string $secret;
    private int $ttl;

    public function __construct(string $secret, int $ttl)
    {
        $this->secret = $secret;
        $this->ttl = $ttl;
    }

    public function encode(array $payload): string
    {
        if ($this->secret === '') {
            throw new InvalidArgumentException('JWT secret is empty.');
        }

        $time = time();

        if (!isset($payload['iat'])) {
            $payload['iat'] = $time;
        }

        if (!isset($payload['exp'])) {
            $payload['exp'] = $time + $this->ttl;
        }

        if (!$this->hasFirebaseJwt()) {
            throw new InvalidArgumentException('JWT library is not available.');
        }

        $jwtClass = 'Firebase\\JWT\\JWT';
        return $jwtClass::encode($payload, $this->secret, 'HS256');
    }

    public function decode(string $token): ?array
    {
        return $this->getPayload($token);
    }

    public function isExpired(string $token): bool
    {
        $payload = $this->getPayload($token);

        if ($payload === null || !isset($payload['exp'])) {
            return true;
        }

        return (int)$payload['exp'] < time();
    }

    public function getPayload(string $token): ?array
    {
        if ($this->secret === '') {
            return null;
        }

        try {
            if (!$this->hasFirebaseJwt()) {
                return null;
            }

            $jwtClass = 'Firebase\\JWT\\JWT';
            $keyClass = 'Firebase\\JWT\\Key';
            $decoded = $jwtClass::decode($token, new $keyClass($this->secret, 'HS256'));
            return json_decode(json_encode($decoded, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            return null;
        }
    }

    public function generateRefreshToken(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(64)), '+/', '-_'), '=');
    }

    private function hasFirebaseJwt(): bool
    {
        return class_exists('Firebase\\JWT\\JWT');
    }
}
