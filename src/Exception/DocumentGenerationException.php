<?php
declare(strict_types=1);

namespace App\Exception;

class DocumentGenerationException extends \RuntimeException
{
    public static function templateNotFound(string $template): self
    {
        return new self("Document template not found: {$template}.", 500);
    }

    public static function generationFailed(string $reason): self
    {
        return new self("Document generation failed: {$reason}.", 500);
    }
}
