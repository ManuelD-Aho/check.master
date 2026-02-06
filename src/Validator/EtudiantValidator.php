<?php

declare(strict_types=1);

namespace App\Validator;

class EtudiantValidator extends AbstractValidator
{
    private const GENRE_ALLOWED = ['M', 'F'];
    private const PROMOTION_PATTERN = '/^\d{4}-\d{4}$/';

    public function validateCreate(array $data): bool
    {
        $this->reset();

        $this->validateRequired('nom', $data['nom'] ?? null, 'nom');
        if (isset($data['nom']) && $data['nom'] !== '') {
            $this->validateString('nom', $data['nom'], 1, 100, 'nom');
        }

        $this->validateRequired('prenom', $data['prenom'] ?? null, 'prénom');
        if (isset($data['prenom']) && $data['prenom'] !== '') {
            $this->validateString('prenom', $data['prenom'], 1, 100, 'prénom');
        }

        $this->validateRequired('email', $data['email'] ?? null, 'email');
        if (isset($data['email']) && $data['email'] !== '') {
            $this->validateEmail('email', $data['email'], 'email');
        }

        $this->validateRequired('date_naissance', $data['date_naissance'] ?? null, 'date de naissance');
        if (isset($data['date_naissance']) && $data['date_naissance'] !== '') {
            if ($this->validateDate('date_naissance', $data['date_naissance'], 'Y-m-d', 'date de naissance')) {
                $this->validateAge($data['date_naissance']);
            }
        }

        $this->validateRequired('genre', $data['genre'] ?? null, 'genre');
        if (isset($data['genre']) && $data['genre'] !== '') {
            $this->validateIn('genre', $data['genre'], self::GENRE_ALLOWED, 'genre');
        }

        $this->validateRequired('promotion', $data['promotion'] ?? null, 'promotion');
        if (isset($data['promotion']) && $data['promotion'] !== '') {
            if ($this->validateRegex('promotion', $data['promotion'], self::PROMOTION_PATTERN, 'promotion')) {
                $this->validatePromotionYears($data['promotion']);
            }
        }

        $this->validateRequired('id_filiere', $data['id_filiere'] ?? null, 'filière');
        if (isset($data['id_filiere']) && $data['id_filiere'] !== '') {
            $this->validateInteger('id_filiere', $data['id_filiere'], 1, null, 'filière');
        }

        return $this->isValid();
    }

    public function validateUpdate(array $data): bool
    {
        $this->reset();

        if (isset($data['nom']) && $data['nom'] !== '') {
            $this->validateString('nom', $data['nom'], 1, 100, 'nom');
        }

        if (isset($data['prenom']) && $data['prenom'] !== '') {
            $this->validateString('prenom', $data['prenom'], 1, 100, 'prénom');
        }

        if (isset($data['email']) && $data['email'] !== '') {
            $this->validateEmail('email', $data['email'], 'email');
        }

        if (isset($data['date_naissance']) && $data['date_naissance'] !== '') {
            if ($this->validateDate('date_naissance', $data['date_naissance'], 'Y-m-d', 'date de naissance')) {
                $this->validateAge($data['date_naissance']);
            }
        }

        if (isset($data['genre']) && $data['genre'] !== '') {
            $this->validateIn('genre', $data['genre'], self::GENRE_ALLOWED, 'genre');
        }

        if (isset($data['promotion']) && $data['promotion'] !== '') {
            if ($this->validateRegex('promotion', $data['promotion'], self::PROMOTION_PATTERN, 'promotion')) {
                $this->validatePromotionYears($data['promotion']);
            }
        }

        if (isset($data['id_filiere']) && $data['id_filiere'] !== '') {
            $this->validateInteger('id_filiere', $data['id_filiere'], 1, null, 'filière');
        }

        return $this->isValid();
    }

    private function validateAge(string $dateNaissance): void
    {
        $birthDate = new \DateTimeImmutable($dateNaissance);
        $now = new \DateTimeImmutable();
        $age = (int) $now->diff($birthDate)->y;

        if ($age < 18) {
            $this->addError('date_naissance', "L'étudiant doit avoir au moins 18 ans.");
        }

        if ($age > 60) {
            $this->addError('date_naissance', "L'étudiant ne peut pas avoir plus de 60 ans.");
        }
    }

    private function validatePromotionYears(string $promotion): void
    {
        $parts = explode('-', $promotion);
        $startYear = (int) $parts[0];
        $endYear = (int) $parts[1];

        if ($endYear !== $startYear + 1) {
            $this->addError('promotion', 'La promotion doit couvrir deux années consécutives (ex: 2024-2025).');
        }
    }
}
