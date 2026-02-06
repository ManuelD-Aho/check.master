<?php
declare(strict_types=1);

namespace App\Exception;

class ValidationException extends \RuntimeException
{
    private array $errors;

    public function __construct(array $errors, string $message = 'Validation failed.', int $code = 422)
    {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public static function withErrors(array $errors): self
    {
        return new self($errors);
    }
}
