<?php

declare(strict_types=1);

namespace App\Validator;

class SoutenanceValidator extends AbstractValidator
{
    private const NOTE_MIN = 0.0;
    private const NOTE_MAX = 20.0;

    public function validateSchedule(array $data): bool
    {
        $this->reset();

        $this->validateRequired('id_jury', $data['id_jury'] ?? null, 'jury');
        if (isset($data['id_jury']) && $data['id_jury'] !== '') {
            $this->validateInteger('id_jury', $data['id_jury'], 1, null, 'jury');
        }

        $this->validateRequired('date_soutenance', $data['date_soutenance'] ?? null, 'date de soutenance');
        if (isset($data['date_soutenance']) && $data['date_soutenance'] !== '') {
            $this->validateDate('date_soutenance', $data['date_soutenance'], 'Y-m-d', 'date de soutenance');
        }

        $this->validateRequired('heure_debut', $data['heure_debut'] ?? null, 'heure de début');
        if (isset($data['heure_debut']) && $data['heure_debut'] !== '') {
            $this->validateRegex('heure_debut', $data['heure_debut'], '/^\d{2}:\d{2}$/', 'heure de début');
        }

        $this->validateRequired('heure_fin', $data['heure_fin'] ?? null, 'heure de fin');
        if (isset($data['heure_fin']) && $data['heure_fin'] !== '') {
            $this->validateRegex('heure_fin', $data['heure_fin'], '/^\d{2}:\d{2}$/', 'heure de fin');
        }

        if (
            isset($data['heure_debut'], $data['heure_fin'])
            && $data['heure_debut'] !== ''
            && $data['heure_fin'] !== ''
        ) {
            $this->validateTimeRange($data['heure_debut'], $data['heure_fin']);
        }

        $this->validateRequired('id_salle', $data['id_salle'] ?? null, 'salle');
        if (isset($data['id_salle']) && $data['id_salle'] !== '') {
            $this->validateInteger('id_salle', $data['id_salle'], 1, null, 'salle');
        }

        return $this->isValid();
    }

    public function validateNotes(array $data): bool
    {
        $this->reset();

        $this->validateRequired('notes', $data['notes'] ?? null, 'notes');

        if (!isset($data['notes']) || !is_array($data['notes'])) {
            return $this->isValid();
        }

        if (count($data['notes']) === 0) {
            $this->addError('notes', 'Au moins une note doit être saisie.');
            return $this->isValid();
        }

        foreach ($data['notes'] as $index => $note) {
            $prefix = "notes[{$index}]";

            if (!isset($note['id_critere']) || $note['id_critere'] === '') {
                $this->addError($prefix . '.id_critere', "Le critère est obligatoire pour la note #{$index}.");
            } else {
                $this->validateInteger($prefix . '.id_critere', $note['id_critere'], 1, null, "critère de la note #{$index}");
            }

            if (!isset($note['note']) || ($note['note'] === '' && $note['note'] !== 0 && $note['note'] !== 0.0)) {
                $this->addError($prefix . '.note', "La note est obligatoire pour l'entrée #{$index}.");
            } else {
                $this->validateNumeric($prefix . '.note', $note['note'], self::NOTE_MIN, self::NOTE_MAX, "note #{$index}");
            }
        }

        return $this->isValid();
    }

    private function validateTimeRange(string $heureDebut, string $heureFin): void
    {
        if (preg_match('/^\d{2}:\d{2}$/', $heureDebut) !== 1 || preg_match('/^\d{2}:\d{2}$/', $heureFin) !== 1) {
            return;
        }

        if ($heureFin <= $heureDebut) {
            $this->addError('heure_fin', "L'heure de fin doit être postérieure à l'heure de début.");
        }
    }
}
