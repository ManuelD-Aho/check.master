<?php
declare(strict_types=1);

namespace App\Exception;

class AuthorizationException extends \RuntimeException
{
    public static function accessDenied(string $permission = ''): self
    {
        $message = $permission !== '' ? "Access denied: {$permission}." : 'Access denied.';

        return new self($message, 403);
    }

    public static function insufficientPermissions(): self
    {
        return new self('Insufficient permissions.', 403);
    }
}
