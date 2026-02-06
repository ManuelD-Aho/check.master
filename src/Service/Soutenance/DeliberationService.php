<?php
declare(strict_types=1);

namespace App\Service\Soutenance;

use App\Entity\Soutenance\DecisionJury;
use App\Entity\Soutenance\ResultatFinal;
use App\Entity\Soutenance\Soutenance;
use App\Entity\Soutenance\TypePv;
use App\Entity\User\Utilisateur;
use App\Repository\Soutenance\ResultatFinalRepository;
use App\Repository\Soutenance\SoutenanceRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

class DeliberationService
{
    private EntityManagerInterface $entityManager;
    private ResultatFinalRepository $resultatRepository;
    private SoutenanceRepository $soutenanceRepository;
    private MoyenneCalculationService $moyenneCalculationService;

    public function __construct(
        EntityManagerInterface $entityManager,
        ResultatFinalRepository $resultatRepository,
        SoutenanceRepository $soutenanceRepository,
        MoyenneCalculationService $moyenneCalculationService
    ) {
        $this->entityManager = $entityManager;
        $this->resultatRepository = $resultatRepository;
        $this->soutenanceRepository = $soutenanceRepository;
        $this->moyenneCalculationService = $moyenneCalculationService;
    }

    public function deliberate(int $soutenanceId, float $moyenneM1, ?float $moyenneS1M2, float $noteMemoire, int $validateurId): ResultatFinal
    {
        $this->entityManager->beginTransaction();

        try {
            $soutenance = $this->entityManager->find(Soutenance::class, $soutenanceId);

            if (!$soutenance instanceof Soutenance) {
                throw new \RuntimeException('Soutenance not found: ' . $soutenanceId);
            }

            $validateur = $this->entityManager->find(Utilisateur::class, $validateurId);

            if ($moyenneS1M2 !== null) {
                $moyenneFinale = $this->moyenneCalculationService->calculateMoyenneFinaleStandard($moyenneM1, $moyenneS1M2, $noteMemoire);
                $typePv = TypePv::STANDARD;
            } else {
                $moyenneFinale = $this->moyenneCalculationService->calculateMoyenneFinaleSimplifiee($moyenneM1, $noteMemoire);
                $typePv = TypePv::SIMPLIFIE;
            }

            $mention = $this->moyenneCalculationService->determineMention($moyenneFinale);
            $decision = $this->moyenneCalculationService->determineDecision($moyenneFinale);

            $now = new DateTimeImmutable();
            $resultat = new ResultatFinal();
            $resultat->setEtudiant($soutenance->getEtudiant())
                ->setAnneeAcademique($soutenance->getJury()?->getAnneeAcademique())
                ->setSoutenance($soutenance)
                ->setNoteMemoire($this->formatAmount($noteMemoire))
                ->setMoyenneM1($this->formatAmount($moyenneM1))
                ->setMoyenneS1M2($moyenneS1M2 !== null ? $this->formatAmount($moyenneS1M2) : null)
                ->setMoyenneFinale($this->formatAmount($moyenneFinale))
                ->setTypePv($typePv)
                ->setDecisionJury($decision === 'ADMIS' ? DecisionJury::ADMIS : DecisionJury::REFUSE)
                ->setDateDeliberation($now)
                ->setValide(true)
                ->setValidateur($validateur)
                ->setDateCreation($now);

            $this->entityManager->persist($resultat);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $resultat;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }

    public function getResultat(int $soutenanceId): ?ResultatFinal
    {
        $results = $this->resultatRepository->findBySoutenance($soutenanceId);

        foreach ($results as $result) {
            if ($result instanceof ResultatFinal) {
                return $result;
            }
        }

        return null;
    }

    public function getResultatsByAnnee(int $anneeId): array
    {
        return $this->resultatRepository->findByAnnee($anneeId);
    }

    public function validateResultat(int $resultatId, int $validateurId): ResultatFinal
    {
        $this->entityManager->beginTransaction();

        try {
            $resultat = $this->resultatRepository->find($resultatId);

            if (!$resultat instanceof ResultatFinal) {
                throw new \RuntimeException('Resultat not found: ' . $resultatId);
            }

            $validateur = $this->entityManager->find(Utilisateur::class, $validateurId);

            $resultat->setValide(true)
                ->setValidateur($validateur);

            $this->entityManager->persist($resultat);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $resultat;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }

    public function invalidateResultat(int $resultatId, string $motif): ResultatFinal
    {
        $this->entityManager->beginTransaction();

        try {
            $resultat = $this->resultatRepository->find($resultatId);

            if (!$resultat instanceof ResultatFinal) {
                throw new \RuntimeException('Resultat not found: ' . $resultatId);
            }

            $resultat->setValide(false)
                ->setValidateur(null);

            $this->entityManager->persist($resultat);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $resultat;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }

    public function getStatistics(int $anneeId): array
    {
        $resultats = $this->resultatRepository->findByAnnee($anneeId);

        $stats = [
            'total' => 0,
            'admis' => 0,
            'refuse' => 0,
            'moyenne_generale' => 0.0,
        ];

        $totalMoyenne = 0.0;

        foreach ($resultats as $resultat) {
            if (!$resultat instanceof ResultatFinal) {
                continue;
            }

            $stats['total']++;
            $totalMoyenne += (float) $resultat->getMoyenneFinale();

            if ($resultat->getDecisionJury() === DecisionJury::ADMIS) {
                $stats['admis']++;
            } else {
                $stats['refuse']++;
            }
        }

        if ($stats['total'] > 0) {
            $stats['moyenne_generale'] = round($totalMoyenne / $stats['total'], 2);
        }

        return $stats;
    }

    private function formatAmount(float $amount): string
    {
        return number_format($amount, 2, '.', '');
    }
}
