<?php
declare(strict_types=1);

namespace App\Service\Commission;

use App\Entity\Commission\DecisionEvaluation;
use App\Entity\Commission\EvaluationRapport;
use App\Entity\Report\Rapport;
use App\Entity\User\Utilisateur;
use App\Repository\Commission\EvaluationRapportRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

class VoteService
{
    private EntityManagerInterface $entityManager;
    private EvaluationRapportRepository $evaluationRepository;

    public function __construct(EntityManagerInterface $entityManager, EvaluationRapportRepository $evaluationRepository)
    {
        $this->entityManager = $entityManager;
        $this->evaluationRepository = $evaluationRepository;
    }

    public function castVote(
        Rapport $rapport,
        Utilisateur $evaluateur,
        DecisionEvaluation $decision,
        ?int $noteQualite = null,
        ?string $commentaire = null,
        ?string $pointsForts = null,
        ?string $pointsAmeliorer = null,
        int $numeroCycle = 1
    ): EvaluationRapport {
        $this->entityManager->beginTransaction();

        try {
            $evaluation = $this->findEvaluation($rapport, $evaluateur, $numeroCycle);
            $now = new DateTimeImmutable();

            if ($evaluation === null) {
                $evaluation = new EvaluationRapport();
                $evaluation->setRapport($rapport)
                    ->setEvaluateur($evaluateur)
                    ->setNumeroCycle($numeroCycle)
                    ->setDateCreation($now);
            }

            $evaluation->setDecisionEvaluation($decision)
                ->setNoteQualite($noteQualite)
                ->setCommentaire($commentaire)
                ->setPointsForts($pointsForts)
                ->setPointsAmeliorer($pointsAmeliorer)
                ->setDateEvaluation($now)
                ->setDateModification($now);

            $this->entityManager->persist($evaluation);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $evaluation;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }

    public function hasVoted(Rapport $rapport, Utilisateur $evaluateur, int $numeroCycle = 1): bool
    {
        $evaluation = $this->findEvaluation($rapport, $evaluateur, $numeroCycle);

        return $evaluation !== null && $evaluation->getDecisionEvaluation() !== null;
    }

    public function getVotesForRapport(Rapport $rapport, int $numeroCycle = 1): array
    {
        if ($rapport->getIdRapport() === null) {
            return [];
        }

        return $this->evaluationRepository->findByCycle($rapport->getIdRapport(), $numeroCycle);
    }

    public function calculateResult(Rapport $rapport, int $numeroCycle = 1): array
    {
        $votes = $this->getVotesForRapport($rapport, $numeroCycle);
        $oui = 0;
        $non = 0;

        foreach ($votes as $vote) {
            if (!$vote instanceof EvaluationRapport) {
                continue;
            }

            $decision = $vote->getDecisionEvaluation();
            if ($decision === DecisionEvaluation::Oui) {
                $oui++;
            } elseif ($decision === DecisionEvaluation::Non) {
                $non++;
            }
        }

        $total = $oui + $non;

        return [
            'total' => $total,
            'oui' => $oui,
            'non' => $non,
            'majorite' => $oui > $non
        ];
    }

    public function isApproved(Rapport $rapport, int $numeroCycle = 1): bool
    {
        $result = $this->calculateResult($rapport, $numeroCycle);

        return (bool)$result['majorite'];
    }

    private function findEvaluation(Rapport $rapport, Utilisateur $evaluateur, int $numeroCycle): ?EvaluationRapport
    {
        $rapportId = $rapport->getIdRapport();
        $evaluateurId = $evaluateur->getIdUtilisateur();

        if ($rapportId === null || $evaluateurId === null) {
            return null;
        }

        return $this->entityManager->createQueryBuilder()
            ->select('e')
            ->from(EvaluationRapport::class, 'e')
            ->join('e.rapport', 'r')
            ->join('e.evaluateur', 'u')
            ->where('r.idRapport = :rapportId')
            ->andWhere('u.idUtilisateur = :evaluateurId')
            ->andWhere('e.numeroCycle = :cycle')
            ->setParameter('rapportId', $rapportId)
            ->setParameter('evaluateurId', $evaluateurId)
            ->setParameter('cycle', $numeroCycle)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
