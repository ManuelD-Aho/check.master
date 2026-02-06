<?php
declare(strict_types=1);

namespace App\Service\Commission;

use App\Entity\Academic\AnneeAcademique;
use App\Entity\Commission\MembreCommission;
use App\Entity\Commission\RoleCommission;
use App\Entity\Commission\SessionCommission;
use App\Entity\Commission\StatutSession;
use App\Entity\User\Utilisateur;
use App\Repository\Commission\MembreCommissionRepository;
use App\Repository\Commission\SessionCommissionRepository;
use App\Repository\Report\RapportRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

class CommissionService
{
    private EntityManagerInterface $entityManager;
    private SessionCommissionRepository $sessionRepository;
    private MembreCommissionRepository $membreRepository;
    private RapportRepository $rapportRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        SessionCommissionRepository $sessionRepository,
        MembreCommissionRepository $membreRepository,
        RapportRepository $rapportRepository
    ) {
        $this->entityManager = $entityManager;
        $this->sessionRepository = $sessionRepository;
        $this->membreRepository = $membreRepository;
        $this->rapportRepository = $rapportRepository;
    }

    public function createSession(
        AnneeAcademique $anneeAcademique,
        int $mois,
        int $annee,
        string $libelle,
        DateTimeInterface $dateDebut,
        DateTimeInterface $dateFin
    ): SessionCommission {
        $this->entityManager->beginTransaction();

        try {
            $session = new SessionCommission();
            $session->setAnneeAcademique($anneeAcademique)
                ->setMoisSession($mois)
                ->setAnneeSession($annee)
                ->setLibelleSession($libelle)
                ->setDateDebut($dateDebut)
                ->setDateFin($dateFin)
                ->setStatutSession(StatutSession::Ouverte)
                ->setDateCreation(new DateTimeImmutable());

            $this->entityManager->persist($session);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $session;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }

    public function closeSession(SessionCommission $session): SessionCommission
    {
        $this->entityManager->beginTransaction();

        try {
            $session->setStatutSession(StatutSession::Fermee);
            $this->entityManager->persist($session);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }

        return $session;
    }

    public function getRapportsForSession(SessionCommission $session): array
    {
        $annee = $session->getAnneeAcademique();
        $anneeId = $annee->getIdAnneeAcademique();

        if ($anneeId === null) {
            return [];
        }

        return $this->rapportRepository->findByAnnee($anneeId);
    }

    public function getMembresByAnnee(int $anneeId): array
    {
        return $this->membreRepository->findByAnnee($anneeId);
    }

    public function addMembre(
        AnneeAcademique $anneeAcademique,
        Utilisateur $utilisateur,
        RoleCommission $role,
        DateTimeInterface $dateNomination
    ): MembreCommission {
        $this->entityManager->beginTransaction();

        try {
            $membre = new MembreCommission();
            $membre->setAnneeAcademique($anneeAcademique)
                ->setUtilisateur($utilisateur)
                ->setRoleCommission($role)
                ->setActif(true)
                ->setDateNomination($dateNomination);

            $this->entityManager->persist($membre);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $membre;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }
}
