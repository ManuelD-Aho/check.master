<?php
declare(strict_types=1);

namespace App\Exception;

class NotFoundException extends \RuntimeException
{
    public static function entity(string $class, int|string $id): self
    {
        return new self("Entity {$class} #{$id} not found.", 404);
    }

    public static function resource(string $name): self
    {
        return new self("Resource {$name} not found.", 404);
    }
}
