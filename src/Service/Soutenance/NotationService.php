<?php
declare(strict_types=1);

namespace App\Service\Soutenance;

use App\Entity\Soutenance\CritereEvaluation;
use App\Entity\Soutenance\NoteSoutenance;
use App\Entity\Soutenance\Soutenance;
use App\Entity\User\Utilisateur;
use App\Repository\Soutenance\CritereEvaluationRepository;
use App\Repository\Soutenance\NoteSoutenanceRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Throwable;

class NotationService
{
    private EntityManagerInterface $entityManager;
    private NoteSoutenanceRepository $noteRepository;
    private CritereEvaluationRepository $critereRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        NoteSoutenanceRepository $noteRepository,
        CritereEvaluationRepository $critereRepository
    ) {
        $this->entityManager = $entityManager;
        $this->noteRepository = $noteRepository;
        $this->critereRepository = $critereRepository;
    }

    public function recordNote(int $soutenanceId, int $critereId, float $note, int $utilisateurId, ?string $commentaire = null): NoteSoutenance
    {
        if ($note < 0 || $note > 20) {
            throw new InvalidArgumentException('La note doit être comprise entre 0 et 20.');
        }

        $this->entityManager->beginTransaction();

        try {
            $soutenance = $this->entityManager->find(Soutenance::class, $soutenanceId);
            $critere = $this->entityManager->find(CritereEvaluation::class, $critereId);
            $utilisateur = $this->entityManager->find(Utilisateur::class, $utilisateurId);

            $existing = $this->entityManager->createQueryBuilder()
                ->select('n')
                ->from(NoteSoutenance::class, 'n')
                ->join('n.soutenance', 's')
                ->join('n.critere', 'c')
                ->where('s.idSoutenance = :soutenanceId')
                ->andWhere('c.idCritere = :critereId')
                ->setParameter('soutenanceId', $soutenanceId)
                ->setParameter('critereId', $critereId)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            $now = new DateTimeImmutable();

            if ($existing instanceof NoteSoutenance) {
                $existing->setNote(number_format($note, 2, '.', ''))
                    ->setCommentaire($commentaire)
                    ->setUtilisateurSaisie($utilisateur)
                    ->setDateSaisie($now);

                $this->entityManager->persist($existing);
                $this->entityManager->flush();
                $this->entityManager->commit();

                return $existing;
            }

            $noteSoutenance = new NoteSoutenance();
            $noteSoutenance->setSoutenance($soutenance)
                ->setCritere($critere)
                ->setNote(number_format($note, 2, '.', ''))
                ->setCommentaire($commentaire)
                ->setUtilisateurSaisie($utilisateur)
                ->setDateSaisie($now);

            $this->entityManager->persist($noteSoutenance);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $noteSoutenance;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }

    public function getNotes(int $soutenanceId): array
    {
        return $this->noteRepository->findBySoutenance($soutenanceId);
    }

    public function getNotesByCritere(int $soutenanceId): array
    {
        $notes = $this->noteRepository->findBySoutenance($soutenanceId);
        $grouped = [];

        foreach ($notes as $note) {
            if (!$note instanceof NoteSoutenance) {
                continue;
            }
            $critere = $note->getCritere();
            if ($critere === null || $critere->getIdCritere() === null) {
                continue;
            }
            $grouped[$critere->getIdCritere()][] = $note;
        }

        return $grouped;
    }

    public function getCriteres(): array
    {
        return $this->critereRepository->findActive();
    }

    public function isNotationComplete(int $soutenanceId): bool
    {
        $criteres = $this->critereRepository->findActive();
        $notes = $this->noteRepository->findBySoutenance($soutenanceId);

        $criteresNotes = [];
        foreach ($notes as $note) {
            if (!$note instanceof NoteSoutenance) {
                continue;
            }
            $critere = $note->getCritere();
            if ($critere === null || $critere->getIdCritere() === null) {
                continue;
            }
            $criteresNotes[$critere->getIdCritere()] = true;
        }

        foreach ($criteres as $critere) {
            if (!$critere instanceof CritereEvaluation || $critere->getIdCritere() === null) {
                continue;
            }
            if (!isset($criteresNotes[$critere->getIdCritere()])) {
                return false;
            }
        }

        return true;
    }

    public function calculateMoyenneSoutenance(int $soutenanceId): ?float
    {
        $notes = $this->noteRepository->findBySoutenance($soutenanceId);

        if (count($notes) === 0) {
            return null;
        }

        $totalPondere = 0.0;
        $totalCoefficients = 0.0;

        foreach ($notes as $note) {
            if (!$note instanceof NoteSoutenance) {
                continue;
            }
            $critere = $note->getCritere();
            if ($critere === null) {
                continue;
            }

            $coefficient = 1.0;
            foreach ($critere->getBaremes() as $bareme) {
                $coefficient = (float) $bareme->getCoefficient();
                break;
            }

            $totalPondere += (float) $note->getNote() * $coefficient;
            $totalCoefficients += $coefficient;
        }

        if ($totalCoefficients === 0.0) {
            return null;
        }

        return $totalPondere / $totalCoefficients;
    }
}
