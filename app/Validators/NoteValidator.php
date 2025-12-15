<?php

declare(strict_types=1);

namespace App\Validators;

/**
 * Validateur Note
 * 
 * Valide les notes attribuées par le jury.
 */
class NoteValidator
{
    private array $errors = [];

    /**
     * Note minimale
     */
    private const NOTE_MIN = 0;

    /**
     * Note maximale
     */
    private const NOTE_MAX = 20;

    /**
     * Valide une note
     */
    public function validate(array $data): bool
    {
        $this->errors = [];

        // Note contenu
        if (!isset($data['note_contenu'])) {
            $this->errors['note_contenu'] = 'La note de contenu est obligatoire';
        } elseif (!$this->isValidNote($data['note_contenu'])) {
            $this->errors['note_contenu'] = 'La note de contenu doit être entre 0 et 20';
        }

        // Note présentation
        if (!isset($data['note_presentation'])) {
            $this->errors['note_presentation'] = 'La note de présentation est obligatoire';
        } elseif (!$this->isValidNote($data['note_presentation'])) {
            $this->errors['note_presentation'] = 'La note de présentation doit être entre 0 et 20';
        }

        // Note travail
        if (!isset($data['note_travail'])) {
            $this->errors['note_travail'] = 'La note de travail est obligatoire';
        } elseif (!$this->isValidNote($data['note_travail'])) {
            $this->errors['note_travail'] = 'La note de travail doit être entre 0 et 20';
        }

        return empty($this->errors);
    }

    /**
     * Vérifie si une note est valide
     */
    private function isValidNote(mixed $note): bool
    {
        if (!is_numeric($note)) {
            return false;
        }

        $note = (float) $note;
        return $note >= self::NOTE_MIN && $note <= self::NOTE_MAX;
    }

    /**
     * Retourne les erreurs
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Retourne la première erreur
     */
    public function getFirstError(): ?string
    {
        return reset($this->errors) ?: null;
    }
}
