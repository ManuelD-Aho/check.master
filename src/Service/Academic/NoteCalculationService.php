<?php

declare(strict_types=1);

namespace App\Service\Academic;

use App\Entity\Academic\TypeNote;
use App\Repository\Academic\NoteRepository;

class NoteCalculationService
{
    private NoteRepository $noteRepository;

    public function __construct(NoteRepository $noteRepository)
    {
        $this->noteRepository = $noteRepository;
    }

    public function calculateMoyenneM1(int $etudiantId, int $anneeId): ?float
    {
        return $this->calculateAverageByType($etudiantId, $anneeId, TypeNote::MOYENNE_M1);
    }

    public function calculateMoyenneS1M2(int $etudiantId, int $anneeId): ?float
    {
        return $this->calculateAverageByType($etudiantId, $anneeId, TypeNote::MOYENNE_S1_M2);
    }

    public function calculateMoyenneUE(int $etudiantId, int $ueId, int $anneeId): ?float
    {
        $notes = $this->noteRepository->findByAnneeAcademique($anneeId);
        $filtered = [];
        $etudiantIdStr = (string) $etudiantId;

        foreach ($notes as $note) {
            $etudiant = $note->getEtudiant();
            $ue = $note->getUniteEnseignement();

            if ($etudiant === null || $ue === null) {
                continue;
            }
            if ($etudiant->getMatriculeEtudiant() !== $etudiantIdStr) {
                continue;
            }
            if ($note->getTypeNote() !== TypeNote::UE) {
                continue;
            }
            if ($note->getNote() === null) {
                continue;
            }
            if ($ue->getIdUe() !== $ueId) {
                continue;
            }

            $filtered[] = $note;
        }

        return $this->averageFromNotes($filtered);
    }

    public function calculateMoyenneGenerale(int $etudiantId, int $anneeId): ?float
    {
        return $this->calculateAverageByType($etudiantId, $anneeId, TypeNote::MOYENNE_GENERALE);
    }

    public function round(float $value, int $precision = 2): float
    {
        return round($value, $precision);
    }

    private function calculateAverageByType(int $etudiantId, int $anneeId, TypeNote $typeNote): ?float
    {
        $notes = $this->noteRepository->findByAnneeAcademique($anneeId);
        $filtered = [];
        $etudiantIdStr = (string) $etudiantId;

        foreach ($notes as $note) {
            if ($note->getTypeNote() !== $typeNote) {
                continue;
            }
            if ($note->getNote() === null) {
                continue;
            }
            $etudiant = $note->getEtudiant();
            if ($etudiant === null) {
                continue;
            }
            if ($etudiant->getMatriculeEtudiant() !== $etudiantIdStr) {
                continue;
            }
            $filtered[] = $note;
        }

        return $this->averageFromNotes($filtered);
    }

    private function averageFromNotes(array $notes): ?float
    {
        if (count($notes) === 0) {
            return null;
        }

        $sum = 0.0;
        $count = 0;

        foreach ($notes as $note) {
            $value = $note->getNote();
            if ($value !== null) {
                $sum += (float) $value;
                $count++;
            }
        }

        if ($count === 0) {
            return null;
        }

        return $this->round($sum / $count);
    }
}
