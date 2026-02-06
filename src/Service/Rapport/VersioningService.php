<?php
declare(strict_types=1);

namespace App\Service\Rapport;

use App\Entity\Report\Rapport;
use App\Entity\Report\TypeVersion;
use App\Entity\Report\VersionRapport;
use App\Repository\Report\VersionRapportRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

class VersioningService
{
    private EntityManagerInterface $entityManager;
    private VersionRapportRepository $versionRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        VersionRapportRepository $versionRepository
    ) {
        $this->entityManager = $entityManager;
        $this->versionRepository = $versionRepository;
    }

    public function createVersion(Rapport $rapport, string $typeVersion, ?string $commentaire = null): VersionRapport
    {
        $this->entityManager->beginTransaction();

        try {
            $latest = $this->versionRepository->findLatestVersion($rapport->getIdRapport());
            $nextNumero = $latest !== null ? $latest->getNumeroVersion() + 1 : 1;

            $version = new VersionRapport();
            $version->setRapport($rapport)
                ->setNumeroVersion($nextNumero)
                ->setContenuHtml($rapport->getContenuHtml())
                ->setTypeVersion(TypeVersion::from($typeVersion))
                ->setCommentaire($commentaire)
                ->setDateCreation(new DateTimeImmutable());

            $this->entityManager->persist($version);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $version;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }

    public function getVersions(int $rapportId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('v')
            ->from(VersionRapport::class, 'v')
            ->join('v.rapport', 'r')
            ->where('r.idRapport = :rapportId')
            ->setParameter('rapportId', $rapportId)
            ->orderBy('v.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getVersion(int $versionId): ?VersionRapport
    {
        return $this->versionRepository->find($versionId);
    }

    public function getLatestVersion(int $rapportId): ?VersionRapport
    {
        return $this->versionRepository->findLatestVersion($rapportId);
    }

    public function restoreVersion(int $rapportId, int $versionId): Rapport
    {
        $this->entityManager->beginTransaction();

        try {
            $version = $this->versionRepository->find($versionId);
            if ($version === null) {
                throw new \InvalidArgumentException('Version not found: ' . $versionId);
            }

            $rapport = $version->getRapport();
            if ($rapport->getIdRapport() !== $rapportId) {
                throw new \InvalidArgumentException('Version does not belong to rapport: ' . $rapportId);
            }

            $rapport->setContenuHtml($version->getContenuHtml())
                ->setDateModification(new DateTimeImmutable());

            $this->entityManager->persist($rapport);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $rapport;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }

    public function countVersions(int $rapportId): int
    {
        return (int)$this->entityManager->createQueryBuilder()
            ->select('COUNT(v)')
            ->from(VersionRapport::class, 'v')
            ->join('v.rapport', 'r')
            ->where('r.idRapport = :rapportId')
            ->setParameter('rapportId', $rapportId)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
