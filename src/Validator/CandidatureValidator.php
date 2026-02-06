<?php

declare(strict_types=1);

namespace App\Validator;

class CandidatureValidator extends AbstractValidator
{
    private const MIN_STAGE_DURATION_DAYS = 90;
    private const MIN_SUJET_LENGTH = 10;
    private const MIN_DESCRIPTION_LENGTH = 100;

    public function validateCreate(array $data): bool
    {
        $this->reset();

        $this->validateRequired('sujet_stage', $data['sujet_stage'] ?? null, 'sujet du stage');
        if (isset($data['sujet_stage']) && $data['sujet_stage'] !== '') {
            $this->validateString('sujet_stage', $data['sujet_stage'], self::MIN_SUJET_LENGTH, 500, 'sujet du stage');
        }

        $this->validateRequired('nom_entreprise', $data['nom_entreprise'] ?? null, "nom de l'entreprise");
        if (isset($data['nom_entreprise']) && $data['nom_entreprise'] !== '') {
            $this->validateString('nom_entreprise', $data['nom_entreprise'], 1, 255, "nom de l'entreprise");
        }

        $this->validateRequired('date_debut', $data['date_debut'] ?? null, 'date de début');
        if (isset($data['date_debut']) && $data['date_debut'] !== '') {
            $this->validateDate('date_debut', $data['date_debut'], 'Y-m-d', 'date de début');
        }

        $this->validateRequired('date_fin', $data['date_fin'] ?? null, 'date de fin');
        if (isset($data['date_fin']) && $data['date_fin'] !== '') {
            $this->validateDate('date_fin', $data['date_fin'], 'Y-m-d', 'date de fin');
        }

        if (
            isset($data['date_debut'], $data['date_fin'])
            && $data['date_debut'] !== ''
            && $data['date_fin'] !== ''
        ) {
            $this->validateDateRange($data['date_debut'], $data['date_fin']);
        }

        $this->validateRequired('nom_encadrant_entreprise', $data['nom_encadrant_entreprise'] ?? null, "nom de l'encadrant entreprise");
        if (isset($data['nom_encadrant_entreprise']) && $data['nom_encadrant_entreprise'] !== '') {
            $this->validateString('nom_encadrant_entreprise', $data['nom_encadrant_entreprise'], 1, 255, "nom de l'encadrant entreprise");
        }

        return $this->isValid();
    }

    public function validateSubmit(array $data): bool
    {
        $this->reset();

        $this->validateRequired('sujet_stage', $data['sujet_stage'] ?? null, 'sujet du stage');
        if (isset($data['sujet_stage']) && $data['sujet_stage'] !== '') {
            $this->validateString('sujet_stage', $data['sujet_stage'], self::MIN_SUJET_LENGTH, 500, 'sujet du stage');
        }

        $this->validateRequired('nom_entreprise', $data['nom_entreprise'] ?? null, "nom de l'entreprise");
        if (isset($data['nom_entreprise']) && $data['nom_entreprise'] !== '') {
            $this->validateString('nom_entreprise', $data['nom_entreprise'], 1, 255, "nom de l'entreprise");
        }

        $this->validateRequired('description_stage', $data['description_stage'] ?? null, 'description du stage');
        if (isset($data['description_stage']) && $data['description_stage'] !== '') {
            $this->validateString('description_stage', $data['description_stage'], self::MIN_DESCRIPTION_LENGTH, 5000, 'description du stage');
        }

        $this->validateRequired('date_debut', $data['date_debut'] ?? null, 'date de début');
        if (isset($data['date_debut']) && $data['date_debut'] !== '') {
            $this->validateDate('date_debut', $data['date_debut'], 'Y-m-d', 'date de début');
        }

        $this->validateRequired('date_fin', $data['date_fin'] ?? null, 'date de fin');
        if (isset($data['date_fin']) && $data['date_fin'] !== '') {
            $this->validateDate('date_fin', $data['date_fin'], 'Y-m-d', 'date de fin');
        }

        if (
            isset($data['date_debut'], $data['date_fin'])
            && $data['date_debut'] !== ''
            && $data['date_fin'] !== ''
        ) {
            $this->validateDateRange($data['date_debut'], $data['date_fin']);
        }

        $this->validateRequired('nom_encadrant_entreprise', $data['nom_encadrant_entreprise'] ?? null, "nom de l'encadrant entreprise");
        if (isset($data['nom_encadrant_entreprise']) && $data['nom_encadrant_entreprise'] !== '') {
            $this->validateString('nom_encadrant_entreprise', $data['nom_encadrant_entreprise'], 1, 255, "nom de l'encadrant entreprise");
        }

        $this->validateRequired('adresse_entreprise', $data['adresse_entreprise'] ?? null, "adresse de l'entreprise");
        if (isset($data['adresse_entreprise']) && $data['adresse_entreprise'] !== '') {
            $this->validateString('adresse_entreprise', $data['adresse_entreprise'], 1, 500, "adresse de l'entreprise");
        }

        $this->validateRequired('email_encadrant_entreprise', $data['email_encadrant_entreprise'] ?? null, "email de l'encadrant entreprise");
        if (isset($data['email_encadrant_entreprise']) && $data['email_encadrant_entreprise'] !== '') {
            $this->validateEmail('email_encadrant_entreprise', $data['email_encadrant_entreprise'], "email de l'encadrant entreprise");
        }

        return $this->isValid();
    }

    private function validateDateRange(string $dateDebut, string $dateFin): void
    {
        $start = \DateTimeImmutable::createFromFormat('Y-m-d', $dateDebut);
        $end = \DateTimeImmutable::createFromFormat('Y-m-d', $dateFin);

        if ($start === false || $end === false) {
            return;
        }

        if ($end <= $start) {
            $this->addError('date_fin', 'La date de fin doit être postérieure à la date de début.');
            return;
        }

        $duration = (int) $start->diff($end)->days;

        if ($duration < self::MIN_STAGE_DURATION_DAYS) {
            $this->addError('date_fin', "La durée du stage doit être d'au moins " . self::MIN_STAGE_DURATION_DAYS . ' jours.');
        }
    }
}
