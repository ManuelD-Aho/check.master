<?php

declare(strict_types=1);

namespace App\Validator;

class NoteValidator extends AbstractValidator
{
    private const NOTE_MIN = 0.0;
    private const NOTE_MAX = 20.0;
    private const TYPES_NOTE_ALLOWED = ['UE', 'ECUE', 'CC', 'TP', 'EXAMEN', 'RATTRAPAGE'];

    public function validateNote(array $data): bool
    {
        $this->reset();

        $this->validateRequired('id_etudiant', $data['id_etudiant'] ?? null, 'étudiant');
        if (isset($data['id_etudiant']) && $data['id_etudiant'] !== '') {
            $this->validateInteger('id_etudiant', $data['id_etudiant'], 1, null, 'étudiant');
        }

        $this->validateRequired('id_element', $data['id_element'] ?? null, 'élément');
        if (isset($data['id_element']) && $data['id_element'] !== '') {
            $this->validateInteger('id_element', $data['id_element'], 1, null, 'élément');
        }

        $this->validateRequired('note', $data['note'] ?? null, 'note');
        if (isset($data['note']) && $data['note'] !== '') {
            $this->validateNumeric('note', $data['note'], self::NOTE_MIN, self::NOTE_MAX, 'note');
        }

        $this->validateRequired('type_note', $data['type_note'] ?? null, 'type de note');
        if (isset($data['type_note']) && $data['type_note'] !== '') {
            $this->validateIn('type_note', $data['type_note'], self::TYPES_NOTE_ALLOWED, 'type de note');
        }

        return $this->isValid();
    }

    public function validateBulk(array $notes): bool
    {
        $this->reset();

        if (count($notes) === 0) {
            $this->addError('notes', 'Au moins une note doit être fournie.');
            return $this->isValid();
        }

        foreach ($notes as $index => $noteData) {
            $prefix = "notes[{$index}]";

            if (!isset($noteData['id_etudiant']) || $noteData['id_etudiant'] === '') {
                $this->addError($prefix . '.id_etudiant', "L'étudiant est obligatoire pour la note #{$index}.");
            } else {
                $this->validateInteger($prefix . '.id_etudiant', $noteData['id_etudiant'], 1, null, "étudiant de la note #{$index}");
            }

            if (!isset($noteData['id_element']) || $noteData['id_element'] === '') {
                $this->addError($prefix . '.id_element', "L'élément est obligatoire pour la note #{$index}.");
            } else {
                $this->validateInteger($prefix . '.id_element', $noteData['id_element'], 1, null, "élément de la note #{$index}");
            }

            if (!isset($noteData['note']) || $noteData['note'] === '') {
                $this->addError($prefix . '.note', "La note est obligatoire pour l'entrée #{$index}.");
            } else {
                $this->validateNumeric($prefix . '.note', $noteData['note'], self::NOTE_MIN, self::NOTE_MAX, "note #{$index}");
            }

            if (!isset($noteData['type_note']) || $noteData['type_note'] === '') {
                $this->addError($prefix . '.type_note', "Le type de note est obligatoire pour la note #{$index}.");
            } else {
                $this->validateIn($prefix . '.type_note', $noteData['type_note'], self::TYPES_NOTE_ALLOWED, "type de la note #{$index}");
            }
        }

        return $this->isValid();
    }
}
