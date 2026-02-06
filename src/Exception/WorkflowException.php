<?php
declare(strict_types=1);

namespace App\Exception;

class WorkflowException extends \RuntimeException
{
    public static function transitionNotAllowed(string $transition, string $currentState): self
    {
        return new self("Transition \"{$transition}\" is not allowed from state \"{$currentState}\".", 409);
    }

    public static function invalidState(string $state): self
    {
        return new self("Invalid workflow state: {$state}.", 409);
    }
}
