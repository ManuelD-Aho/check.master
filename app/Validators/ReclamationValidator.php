<?php

declare(strict_types=1);

namespace App\Validators;

/**
 * Validateur pour les réclamations
 */
class ReclamationValidator
{
    private array $errors = [];

    public function validate(array $data): bool
    {
        $this->errors = [];

        if (empty($data['sujet']) || strlen($data['sujet']) < 3) {
            $this->errors['sujet'] = "Le sujet est requis (min 3 caractères)";
        }

        if (empty($data['message']) || strlen($data['message']) < 10) {
            $this->errors['message'] = "Le message est requis (min 10 caractères)";
        }

        if (empty($data['type_reclamation'])) {
            $this->errors['type_reclamation'] = "Le type de réclamation est requis";
        }

        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
