<?php
declare(strict_types=1);

namespace App\Service\Soutenance;

use App\Entity\Soutenance\Soutenance;
use App\Repository\Soutenance\SalleRepository;
use App\Repository\Soutenance\SoutenanceRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class PlanningService
{
    private EntityManagerInterface $entityManager;
    private SoutenanceRepository $soutenanceRepository;
    private SalleRepository $salleRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        SoutenanceRepository $soutenanceRepository,
        SalleRepository $salleRepository
    ) {
        $this->entityManager = $entityManager;
        $this->soutenanceRepository = $soutenanceRepository;
        $this->salleRepository = $salleRepository;
    }

    public function getPlanning(int $anneeId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('s')
            ->from(Soutenance::class, 's')
            ->join('s.jury', 'j')
            ->join('j.anneeAcademique', 'a')
            ->where('a.idAnneeAcademique = :anneeId')
            ->setParameter('anneeId', $anneeId)
            ->orderBy('s.dateSoutenance', 'ASC')
            ->addOrderBy('s.heureDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getPlanningByDate(string $date): array
    {
        $dateTime = new DateTimeImmutable($date);

        return $this->entityManager->createQueryBuilder()
            ->select('s')
            ->from(Soutenance::class, 's')
            ->where('s.dateSoutenance = :date')
            ->setParameter('date', $dateTime)
            ->orderBy('s.heureDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getPlanningBySalle(int $salleId, string $date): array
    {
        $dateTime = new DateTimeImmutable($date);

        return $this->entityManager->createQueryBuilder()
            ->select('s')
            ->from(Soutenance::class, 's')
            ->join('s.salle', 'sa')
            ->where('sa.idSalle = :salleId')
            ->andWhere('s.dateSoutenance = :date')
            ->setParameter('salleId', $salleId)
            ->setParameter('date', $dateTime)
            ->orderBy('s.heureDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function checkAvailability(int $salleId, string $date, string $heureDebut, string $heureFin): bool
    {
        $dateTime = new DateTimeImmutable($date);
        $debut = new DateTimeImmutable($heureDebut);
        $fin = new DateTimeImmutable($heureFin);

        $conflicts = $this->entityManager->createQueryBuilder()
            ->select('COUNT(s.idSoutenance)')
            ->from(Soutenance::class, 's')
            ->join('s.salle', 'sa')
            ->where('sa.idSalle = :salleId')
            ->andWhere('s.dateSoutenance = :date')
            ->andWhere('s.heureDebut < :heureFin')
            ->andWhere('(s.heureFin IS NULL OR s.heureFin > :heureDebut)')
            ->setParameter('salleId', $salleId)
            ->setParameter('date', $dateTime)
            ->setParameter('heureDebut', $debut)
            ->setParameter('heureFin', $fin)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $conflicts === 0;
    }

    public function getAvailableSlots(int $salleId, string $date): array
    {
        $soutenances = $this->getPlanningBySalle($salleId, $date);

        $occupiedSlots = [];
        foreach ($soutenances as $soutenance) {
            if (!$soutenance instanceof Soutenance) {
                continue;
            }
            $startHour = (int) $soutenance->getHeureDebut()->format('H');
            $endHour = $soutenance->getHeureFin() !== null
                ? (int) $soutenance->getHeureFin()->format('H')
                : $startHour + 1;

            for ($h = $startHour; $h < $endHour; $h++) {
                $occupiedSlots[$h] = true;
            }
        }

        $available = [];
        for ($hour = 8; $hour < 18; $hour++) {
            if (!isset($occupiedSlots[$hour])) {
                $available[] = [
                    'heureDebut' => sprintf('%02d:00', $hour),
                    'heureFin' => sprintf('%02d:00', $hour + 1),
                ];
            }
        }

        return $available;
    }

    public function getStatistics(int $anneeId): array
    {
        $soutenances = $this->getPlanning($anneeId);

        $stats = [
            'total' => 0,
            'programmee' => 0,
            'en_cours' => 0,
            'terminee' => 0,
            'reportee' => 0,
            'annulee' => 0,
        ];

        foreach ($soutenances as $soutenance) {
            if (!$soutenance instanceof Soutenance) {
                continue;
            }
            $stats['total']++;
            $statut = $soutenance->getStatutSoutenance()->value;
            if (isset($stats[$statut])) {
                $stats[$statut]++;
            }
        }

        return $stats;
    }
}
