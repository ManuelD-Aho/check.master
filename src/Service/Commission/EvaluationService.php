<?php
declare(strict_types=1);

namespace App\Service\Commission;

use App\Entity\Commission\DecisionEvaluation;
use App\Entity\Commission\EvaluationRapport;
use App\Repository\Commission\EvaluationRapportRepository;
use App\Repository\Commission\MembreCommissionRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

class EvaluationService
{
    private EntityManagerInterface $entityManager;
    private EvaluationRapportRepository $evaluationRepository;
    private MembreCommissionRepository $membreRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        EvaluationRapportRepository $evaluationRepository,
        MembreCommissionRepository $membreRepository
    ) {
        $this->entityManager = $entityManager;
        $this->evaluationRepository = $evaluationRepository;
        $this->membreRepository = $membreRepository;
    }

    public function evaluate(int $rapportId, int $membreId, string $decision, ?string $commentaire = null): EvaluationRapport
    {
        $this->entityManager->beginTransaction();

        try {
            $rapport = $this->entityManager->getReference('App\Entity\Report\Rapport', $rapportId);
            $membre = $this->membreRepository->find($membreId);

            if ($membre === null) {
                throw new \InvalidArgumentException('Membre not found: ' . $membreId);
            }

            $now = new DateTimeImmutable();
            $evaluation = new EvaluationRapport();
            $evaluation->setRapport($rapport)
                ->setEvaluateur($membre->getUtilisateur())
                ->setDecisionEvaluation(DecisionEvaluation::from($decision))
                ->setCommentaire($commentaire)
                ->setDateEvaluation($now)
                ->setDateCreation($now)
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

    public function getEvaluationsForRapport(int $rapportId): array
    {
        return $this->evaluationRepository->findByRapport($rapportId);
    }

    public function getEvaluationsByMembre(int $membreId): array
    {
        $membre = $this->membreRepository->find($membreId);
        if ($membre === null) {
            return [];
        }

        $utilisateurId = $membre->getUtilisateur()->getIdUtilisateur();

        return $this->evaluationRepository->findByEvaluateur($utilisateurId);
    }

    public function hasAlreadyEvaluated(int $rapportId, int $membreId): bool
    {
        $membre = $this->membreRepository->find($membreId);
        if ($membre === null) {
            return false;
        }

        $utilisateurId = $membre->getUtilisateur()->getIdUtilisateur();

        $evaluation = $this->entityManager->createQueryBuilder()
            ->select('e')
            ->from(EvaluationRapport::class, 'e')
            ->join('e.rapport', 'r')
            ->join('e.evaluateur', 'u')
            ->where('r.idRapport = :rapportId')
            ->andWhere('u.idUtilisateur = :utilisateurId')
            ->setParameter('rapportId', $rapportId)
            ->setParameter('utilisateurId', $utilisateurId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $evaluation !== null;
    }

    public function getEvaluationStatus(int $rapportId): array
    {
        $activeMembers = $this->membreRepository->findActive();
        $evaluations = $this->evaluationRepository->findByRapport($rapportId);

        $totalMembers = count($activeMembers);
        $evaluated = count($evaluations);
        $pending = $totalMembers - $evaluated;

        $decisions = [];
        foreach ($evaluations as $evaluation) {
            if (!$evaluation instanceof EvaluationRapport) {
                continue;
            }

            $decision = $evaluation->getDecisionEvaluation();
            if ($decision !== null) {
                $key = $decision->value;
                $decisions[$key] = ($decisions[$key] ?? 0) + 1;
            }
        }

        return [
            'total_members' => $totalMembers,
            'evaluated' => $evaluated,
            'pending' => max(0, $pending),
            'decisions' => $decisions,
        ];
    }

    public function isEvaluationComplete(int $rapportId): bool
    {
        $status = $this->getEvaluationStatus($rapportId);

        return $status['pending'] === 0 && $status['total_members'] > 0;
    }
}
