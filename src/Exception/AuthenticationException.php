<?php
declare(strict_types=1);

namespace App\Exception;

class AuthenticationException extends \RuntimeException
{
    public static function invalidCredentials(): self
    {
        return new self('Invalid credentials.', 401);
    }

    public static function accountLocked(): self
    {
        return new self('Account is locked.', 423);
    }

    public static function sessionExpired(): self
    {
        return new self('Session has expired.', 401);
    }
}
