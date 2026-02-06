<?php

declare(strict_types=1);

namespace App\Validator;

class RapportValidator extends AbstractValidator
{
    private const MIN_TITRE_LENGTH = 5;
    private const MAX_TITRE_LENGTH = 500;
    private const MIN_RESUME_LENGTH = 50;
    private const MAX_RESUME_LENGTH = 2000;

    public function validateCreate(array $data): bool
    {
        $this->reset();

        $this->validateRequired('titre', $data['titre'] ?? null, 'titre');
        if (isset($data['titre']) && $data['titre'] !== '') {
            $this->validateString('titre', $data['titre'], self::MIN_TITRE_LENGTH, self::MAX_TITRE_LENGTH, 'titre');
        }

        return $this->isValid();
    }

    public function validateContent(array $data): bool
    {
        $this->reset();

        $this->validateRequired('contenu', $data['contenu'] ?? null, 'contenu');
        if (isset($data['contenu']) && $data['contenu'] !== '') {
            if (!is_string($data['contenu'])) {
                $this->addError('contenu', 'Le champ contenu doit être une chaîne de caractères.');
            } else {
                $stripped = strip_tags($data['contenu']);
                $stripped = trim($stripped);

                if ($stripped === '') {
                    $this->addError('contenu', 'Le contenu ne peut pas être vide.');
                }
            }
        }

        return $this->isValid();
    }

    public function validateMetadata(array $data): bool
    {
        $this->reset();

        $this->validateRequired('titre', $data['titre'] ?? null, 'titre');
        if (isset($data['titre']) && $data['titre'] !== '') {
            $this->validateString('titre', $data['titre'], self::MIN_TITRE_LENGTH, self::MAX_TITRE_LENGTH, 'titre');
        }

        $this->validateRequired('resume', $data['resume'] ?? null, 'résumé');
        if (isset($data['resume']) && $data['resume'] !== '') {
            $this->validateString('resume', $data['resume'], self::MIN_RESUME_LENGTH, self::MAX_RESUME_LENGTH, 'résumé');
        }

        $this->validateRequired('mots_cles', $data['mots_cles'] ?? null, 'mots-clés');
        if (isset($data['mots_cles'])) {
            if (is_string($data['mots_cles']) && $data['mots_cles'] !== '') {
                $this->validateString('mots_cles', $data['mots_cles'], 1, 500, 'mots-clés');
            } elseif (is_array($data['mots_cles'])) {
                if (count($data['mots_cles']) === 0) {
                    $this->addError('mots_cles', 'Le champ mots-clés est obligatoire.');
                }
            }
        }

        return $this->isValid();
    }
}
