<?php

declare(strict_types=1);

namespace App\Service\Academic;

use App\Entity\Academic\AnneeAcademique;
use App\Repository\Academic\AnneeAcademiqueRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

class AnneeAcademiqueService
{
    private EntityManagerInterface $entityManager;
    private AnneeAcademiqueRepository $anneeAcademiqueRepository;

    public function __construct(EntityManagerInterface $entityManager, AnneeAcademiqueRepository $anneeAcademiqueRepository)
    {
        $this->entityManager = $entityManager;
        $this->anneeAcademiqueRepository = $anneeAcademiqueRepository;
    }

    public function getActiveYear(): ?AnneeAcademique
    {
        $result = $this->anneeAcademiqueRepository->findOneBy(['estActive' => true]);

        return $result instanceof AnneeAcademique ? $result : null;
    }

    public function setActiveYear(int $anneeId): AnneeAcademique
    {
        $annee = $this->anneeAcademiqueRepository->find($anneeId);

        if (!$annee instanceof AnneeAcademique) {
            throw new \InvalidArgumentException('Année académique non trouvée avec l\'id : ' . $anneeId);
        }

        $this->entityManager->beginTransaction();

        try {
            $activeYears = $this->anneeAcademiqueRepository->findActive();
            foreach ($activeYears as $activeYear) {
                if ($activeYear instanceof AnneeAcademique) {
                    $activeYear->setEstActive(false);
                    $activeYear->setDateModification(new DateTimeImmutable());
                    $this->entityManager->persist($activeYear);
                }
            }

            $annee->setEstActive(true);
            $annee->setDateModification(new DateTimeImmutable());
            $this->entityManager->persist($annee);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }

        return $annee;
    }

    public function create(array $data): AnneeAcademique
    {
        $this->entityManager->beginTransaction();

        try {
            $now = new DateTimeImmutable();
            $annee = new AnneeAcademique();
            $annee->setLibelleAnnee((string) $data['libelle'])
                ->setDateDebut($data['date_debut'])
                ->setDateFin($data['date_fin'])
                ->setEstActive(false)
                ->setEstOuverteInscription(true)
                ->setDateCreation($now)
                ->setDateModification($now);

            $this->entityManager->persist($annee);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $annee;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }

    public function update(int $id, array $data): ?AnneeAcademique
    {
        $annee = $this->anneeAcademiqueRepository->find($id);

        if (!$annee instanceof AnneeAcademique) {
            return null;
        }

        $this->entityManager->beginTransaction();

        try {
            if (isset($data['libelle'])) {
                $annee->setLibelleAnnee((string) $data['libelle']);
            }
            if (isset($data['date_debut'])) {
                $annee->setDateDebut($data['date_debut']);
            }
            if (isset($data['date_fin'])) {
                $annee->setDateFin($data['date_fin']);
            }

            $annee->setDateModification(new DateTimeImmutable());
            $this->entityManager->persist($annee);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }

        return $annee;
    }

    public function findAll(): array
    {
        return $this->anneeAcademiqueRepository->findBy([], ['dateDebut' => 'DESC']);
    }

    public function findById(int $id): ?AnneeAcademique
    {
        $result = $this->anneeAcademiqueRepository->find($id);

        return $result instanceof AnneeAcademique ? $result : null;
    }
}
