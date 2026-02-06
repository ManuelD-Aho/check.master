<?php
declare(strict_types=1);

namespace App\Service\Soutenance;

use App\Entity\Academic\AnneeAcademique;
use App\Entity\Soutenance\CompositionJury;
use App\Entity\Soutenance\Jury;
use App\Entity\Soutenance\RoleJury;
use App\Entity\Soutenance\StatutJury;
use App\Entity\Staff\Enseignant;
use App\Entity\Student\Etudiant;
use App\Entity\User\Utilisateur;
use App\Repository\Soutenance\CompositionJuryRepository;
use App\Repository\Soutenance\JuryRepository;
use App\Repository\Soutenance\RoleJuryRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

class JuryService
{
    private EntityManagerInterface $entityManager;
    private JuryRepository $juryRepository;
    private CompositionJuryRepository $compositionRepository;
    private RoleJuryRepository $roleJuryRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        JuryRepository $juryRepository,
        CompositionJuryRepository $compositionRepository,
        RoleJuryRepository $roleJuryRepository
    ) {
        $this->entityManager = $entityManager;
        $this->juryRepository = $juryRepository;
        $this->compositionRepository = $compositionRepository;
        $this->roleJuryRepository = $roleJuryRepository;
    }

    public function createJury(
        Etudiant $etudiant,
        AnneeAcademique $anneeAcademique,
        Utilisateur $createur,
        ?DateTimeInterface $dateCreation = null
    ): Jury {
        $this->entityManager->beginTransaction();

        try {
            $jury = new Jury();
            $jury->setEtudiant($etudiant)
                ->setAnneeAcademique($anneeAcademique)
                ->setCreateur($createur)
                ->setStatutJury(StatutJury::EN_COMPOSITION)
                ->setDateCreation($dateCreation ?? new DateTimeImmutable());

            $this->entityManager->persist($jury);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $jury;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }

    public function addMember(
        Jury $jury,
        Enseignant $enseignant,
        RoleJury $roleJury,
        Utilisateur $affecteur,
        ?string $commentaire = null
    ): CompositionJury {
        $this->entityManager->beginTransaction();

        try {
            $composition = new CompositionJury();
            $composition->setJury($jury)
                ->setEnseignant($enseignant)
                ->setRoleJury($roleJury)
                ->setCommentaire($commentaire)
                ->setDateAffectation(new DateTimeImmutable())
                ->setAffecteur($affecteur);

            $this->entityManager->persist($composition);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $composition;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }

    public function addExternalMember(
        Jury $jury,
        RoleJury $roleJury,
        string $nom,
        string $prenom,
        string $fonction,
        string $email,
        string $telephone,
        ?string $entreprise,
        Utilisateur $affecteur,
        ?string $commentaire = null
    ): CompositionJury {
        $this->entityManager->beginTransaction();

        try {
            $composition = new CompositionJury();
            $composition->setJury($jury)
                ->setRoleJury($roleJury)
                ->setNomExterne($nom)
                ->setPrenomExterne($prenom)
                ->setFonctionExterne($fonction)
                ->setEmailExterne($email)
                ->setTelephoneExterne($telephone)
                ->setEntrepriseExterne($entreprise)
                ->setCommentaire($commentaire)
                ->setDateAffectation(new DateTimeImmutable())
                ->setAffecteur($affecteur);

            $this->entityManager->persist($composition);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $composition;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }

    public function validateJury(Jury $jury): Jury
    {
        $this->entityManager->beginTransaction();

        try {
            $jury->setStatutJury(StatutJury::VALIDE)
                ->setDateValidation(new DateTimeImmutable());

            $this->entityManager->persist($jury);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }

        return $jury;
    }

    public function isComplete(Jury $jury): bool
    {
        if ($jury->getIdJury() === null) {
            return false;
        }

        $rolesObligatoires = $this->roleJuryRepository->findObligatoires();
        $compositions = $this->compositionRepository->findByJury($jury->getIdJury());

        $rolesPresents = [];
        foreach ($compositions as $composition) {
            if (!$composition instanceof CompositionJury) {
                continue;
            }
            $role = $composition->getRoleJury();
            if ($role === null || $role->getIdRoleJury() === null) {
                continue;
            }
            $rolesPresents[$role->getIdRoleJury()] = true;
        }

        foreach ($rolesObligatoires as $role) {
            if (!$role instanceof RoleJury || $role->getIdRoleJury() === null) {
                continue;
            }
            if (!isset($rolesPresents[$role->getIdRoleJury()])) {
                return false;
            }
        }

        return true;
    }
}
