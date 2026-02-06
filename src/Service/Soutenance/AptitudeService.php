<?php
declare(strict_types=1);

namespace App\Service\Soutenance;

use App\Entity\Soutenance\AptitudeSoutenance;
use App\Entity\Staff\Enseignant;
use App\Entity\Student\Etudiant;
use App\Repository\Soutenance\AptitudeSoutenanceRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

class AptitudeService
{
    private EntityManagerInterface $entityManager;
    private AptitudeSoutenanceRepository $aptitudeRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        AptitudeSoutenanceRepository $aptitudeRepository
    ) {
        $this->entityManager = $entityManager;
        $this->aptitudeRepository = $aptitudeRepository;
    }

    public function validate(int $etudiantId, int $encadreurId, bool $estApte, ?string $commentaire = null): AptitudeSoutenance
    {
        $this->entityManager->beginTransaction();

        try {
            $etudiant = $this->entityManager->find(Etudiant::class, $etudiantId);
            $encadreur = $this->entityManager->find(Enseignant::class, $encadreurId);

            $now = new DateTimeImmutable();
            $aptitude = new AptitudeSoutenance();
            $aptitude->setEtudiant($etudiant)
                ->setEncadreur($encadreur)
                ->setEstApte($estApte)
                ->setCommentaire($commentaire)
                ->setDateValidation($now)
                ->setDateCreation($now);

            $this->entityManager->persist($aptitude);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $aptitude;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }

    public function getByEtudiant(int $etudiantId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('a')
            ->from(AptitudeSoutenance::class, 'a')
            ->join('a.etudiant', 'e')
            ->where('e.matriculeEtudiant = :etudiantId')
            ->setParameter('etudiantId', $etudiantId)
            ->orderBy('a.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getLatest(int $etudiantId): ?AptitudeSoutenance
    {
        $result = $this->entityManager->createQueryBuilder()
            ->select('a')
            ->from(AptitudeSoutenance::class, 'a')
            ->join('a.etudiant', 'e')
            ->where('e.matriculeEtudiant = :etudiantId')
            ->setParameter('etudiantId', $etudiantId)
            ->orderBy('a.dateCreation', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $result instanceof AptitudeSoutenance ? $result : null;
    }

    public function isApte(int $etudiantId): bool
    {
        $latest = $this->getLatest($etudiantId);

        if ($latest === null) {
            return false;
        }

        return $latest->getEstApte() === true;
    }

    public function revoke(int $aptitudeId, string $motif): AptitudeSoutenance
    {
        $this->entityManager->beginTransaction();

        try {
            $aptitude = $this->aptitudeRepository->find($aptitudeId);

            if (!$aptitude instanceof AptitudeSoutenance) {
                throw new \RuntimeException('Aptitude not found: ' . $aptitudeId);
            }

            $aptitude->setEstApte(false)
                ->setCommentaire($motif)
                ->setDateValidation(new DateTimeImmutable());

            $this->entityManager->persist($aptitude);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $aptitude;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }
}
